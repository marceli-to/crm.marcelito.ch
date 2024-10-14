<?php
namespace App\Actions\Expense;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Actions\Expense\CreateExpense;
use App\Actions\Expense\UploadedReceipt;

class ProcessMails
{
  protected $allowed_mime_types = [
    'image/jpeg',
    'image/png',
    'image/jpg',
    'application/pdf'
  ];

  public function execute($messages)
  {
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
            $data = explode(':', $message->getSubject()[0]);
            $expense = (new CreateExpense())->execute([
              'title' => $data[0],
              'description' => $data[1],
              'amount' => $data[2],
              'currency_id' => 1,
            ]);

            // Get full path
            $receipt = [];
            $receipt['path'] = Storage::path('mailbox-temp/' . $attachment->getAttributes()['name']);

            // Get extension from mime type
            $extension = explode('/', $attachment->getAttributes()['content_type'])[1];

            // Turn jpeg into jpg
            $receipt['extension'] = str_replace('jpeg', 'jpg', $extension);

            // Upload receipt
            $filename = (new UploadedReceipt())->execute($expense->number, $receipt);
            $expense->receipt = $filename;
            $expense->save();

            // Delete the message
            // $message->delete();
          }
        }
      }
    }
  }
}