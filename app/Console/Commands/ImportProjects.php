<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\Company;
use App\Models\Project;
use App\Models\Status;
use App\Models\Invoice;

class ImportProjects extends Command
{
  protected $signature = 'import:projects';

  protected $description = 'Loads a json file from /storage/app/public/';

  public function handle()
  {
    // ask for the file name
    $file = $this->ask('Enter the name of the file you want to load (without the .json extension):');

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
      // get or create company
      $company = Company::firstOrCreate(['acronym' => $item['acronym']], [
        'name' => $item['name'],
        'acronym' => $item['acronym'],
        'byline' => $item['byline'],
        'street' => $item['street'],
        'zip' => $item['zip'],
        'city' => $item['city'],
      ]);

      // set rate
      $rate = $item['rate'];
      switch ($rate)
      {
        case '100.00':
          $rate_id = 1;
          break;
        case '125.00':
          $rate_id = 2;
          break;
        case '135.00':
          $rate_id = 3;
          break;
        case '150.00':
          $rate_id = 4;
          break;
        default:
          $rate_id = 3;
          break;
      }

      // create project
      $project = Project::create([
        'name' => $item['title'],
        'budget' => $item['total'],
        'rate_id' => $rate_id,
        'company_id' => $company->id,
        'principal_id' => $company->id,
        'archived_at' => $item['state_id'] == 3 ? now() : null,
        'created_at' => $item['created_at'],
        'updated_at' => $item['updated_at'],
        'deleted_at' => $item['state_id'] == 6 ? $item['updated_at'] : null,
      ]);

      // create invoice
      // the new version does not have a status_id of 5 (closed), so we need to set it to 3 (paid)
      // we also need to set the status_id of 6 (cancelled) to 5 (cancelled)
      $status_id = $item['state_id'] == 5 ? 3 : ($item['state_id'] == 6 ? 5 : $item['state_id']);
      $invoice = Invoice::create([
        'date' => $item['date'],
        'number' => $item['number'],
        'title' => $item['title'],
        'text' => $item['text'],
        'cancellation_reason' => $item['remarks'],
        'total' => $item['total'],
        'vat' => $item['vat'],
        'grand_total' => $item['grandtotal'],
        'company_id' => $company->id,
        'project_id' => $project->id,
        'status_id' => $status_id,
        'due_at' => $item['date_due'],
        'paid_at' => $item['date_paid'],
        'created_at' => $item['created_at'],
        'updated_at' => $item['updated_at'],
        'deleted_at' => $item['state_id'] == 6 ? $item['updated_at'] : null,
      ]);
    }

  }
}
