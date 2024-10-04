<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Actions\FetchMailbox;
use Illuminate\Support\Facades\Storage;
use App\Models\Expense;
use App\Actions\UploadedExpenseReceipt;

class MailboxTest extends Command
{
  protected $signature = 'mailbox:test';

  public function handle()
  {
    $messages = (new FetchMailbox())->execute();
    if ($messages)
    {
      foreach($messages as $message)
      {
        if ($message->hasAttachments())
        {
          $attachments = $message->getAttachments();
          foreach($attachments as $attachment)
          {
            if (Storage::put('mailbox-temp/' . $attachment->getAttributes()['name'], $attachment->content))
            {
              // get full path
              $receipt = [];
              $receipt['path'] = Storage::path('mailbox-temp/' . $attachment->getAttributes()['name']);
              $receipt['name'] = $attachment->getAttributes()['name'];
              $receipt['size'] = $attachment->getAttributes()['size'];

              // get extension from mime type, turn jpeg to jpg
              $extension = str_replace('jpeg', 'jpg', explode('/', $attachment->getAttributes()['content_type'])[1]);
              $receipt['extension'] = $extension;

              // Generate data from subject (Title and Description / Amount)
              $subject = explode(':', $message->getSubject()[0]);
              $title = $subject[0];
              $description = $subject[0];
              $amount = $subject[1];

              // Create expense
              $expense = Expense::create([
                'date' => date('Y-m-d'),
                'title' => $title,
                'description' => $description,
                'amount' => $amount,
                'currency_id' => 1,
              ]);

              // Set the expense number
              $expense->number = date('y', time()) . '.' . str_pad($expense->id, 4, "0", STR_PAD_LEFT);
              $expense->save();

              // Upload receipt
              $filename = (new UploadedExpenseReceipt())->execute($expense, $receipt);
              $expense->receipt = $filename;
              $expense->save();
            }
          }

          // TODO: delete message from mailbox
        }
      }
    }
  }
}
