<?php
namespace App\Actions\Expense;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Client;

class FetchMails
{
  public function execute()
  {
    $client_manager = new ClientManager(config_path() . '/imap.php');
    $client = $client_manager->account('default');
    $client->connect();
    $folder = $client->getFolderByName('INBOX');
    return $folder->messages()->all()->get() ?? [];
  }
}