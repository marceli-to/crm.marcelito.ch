<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\Expense;

class ImportExpenses extends Command
{
  protected $signature = 'import:expenses';

  protected $description = 'Loads a json file from /storage/app/public/';

  public function handle()
  {
    // ask for the file name
    $file = $this->ask('Enter the name of the file you want to load (without the .json extension):');

    if (!$file) {
      $file = 'expenses';
    }

    // check if the file exists
    if (!\Storage::disk('public')->exists($file . '.json')) {
      $this->error('The file does not exist.');
      return;
    }

    // get the contents of the file
    $json = \Storage::disk('public')->get($file . '.json');

    $data = json_decode($json, true);

    // loop over all the data
    foreach ($data as $item)
    {
      Expense::create([
        'number' => $item['number'],
        'date' => $item['date'],
        'title' => $item['title'],
        'description' => $item['description'],
        'currency_id' => $item['currency'] == 'CHF' ? 1 : 2,
        'amount' => $item['amount'],
        'receipt' => $item['number'] . '.jpg',
      ]);
    }
  }
}
