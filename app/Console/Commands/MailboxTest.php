<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Actions\Expense\FetchMails;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Expense;
use App\Actions\UploadedExpenseReceipt;

class MailboxTest extends Command
{
  protected $signature = 'mailbox:test';

  protected $allowed_mime_types = [
    'image/jpeg',
    'image/png',
    'image/jpg',
    'application/pdf'
  ];

  public function handle()
  {
    $messages = (new FetchMails())->execute();
    
    foreach($messages as $message)
    {
      if ($message->hasAttachments())
      {
        $attachments = $message->getAttachments();
        foreach($attachments as $attachment)
        {
          // Check if the attachment is allowed
          // Log error if not and skip to next attachment
          if (!in_array($attachment->getAttributes()['content_type'], $this->allowed_mime_types))
          {
            Log::error('Attachment is not allowed', [
              'attachment' => $attachment->getAttributes()
            ]);
            // Delete the message
            $message->delete();
            continue;
          }

          // Save the attachment to the mailbox-temp folder
          if (Storage::put('mailbox-temp/' . $attachment->getAttributes()['name'], $attachment->content))
          {
            // Generate expense data from subject (Title and Description / Amount)
            $expense_data = explode(':', $message->getSubject()[0]);

            // Create expense
            $expense = Expense::create([
              'date' => date('Y-m-d'),
              'title' => $expense_data[0] ?? 'no title',
              'description' => $expense_data[0] ?? 'no description',
              'amount' => $expense_data[1] ?? 0,
              'currency_id' => 1,
            ]);

            // Set the expense number
            // Format: YYYY.XXXX
            $expense->number = date('y', time()) . '.' . str_pad($expense->id, 4, "0", STR_PAD_LEFT);
            $expense->save();

            // Get full path
            $receipt = [];
            $receipt['path'] = Storage::path('mailbox-temp/' . $attachment->getAttributes()['name']);

            // Get extension from mime type
            $extension = explode('/', $attachment->getAttributes()['content_type'])[1];

            // Turn jpeg into jpg
            $receipt['extension'] = str_replace('jpeg', 'jpg', $extension);

            // Upload receipt
            $filename = (new UploadedExpenseReceipt())->execute($expense->number, $receipt);
            $expense->receipt = $filename;
            $expense->save();

            // Delete the message
            $message->delete();
          }
        }
      }
    }
  }
}
