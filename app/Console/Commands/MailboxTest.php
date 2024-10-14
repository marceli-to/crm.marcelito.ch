<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Actions\Expense\FetchMails;
use App\Actions\Expense\ProcessMails;

class MailboxTest extends Command
{
  protected $signature = 'mailbox:test';

  public function handle()
  {
    (new ProcessMails())->execute(
      (new FetchMails())->execute()
    );
  }
}
