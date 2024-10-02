<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\Company;
use App\Models\Project;

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
      // check if company exists by acronym
      $company = Company::where('acronym', $item['acronym'])->first();
      if (!$company) {
        $company = Company::create([
          'name' => $item['name'],
          'acronym' => $item['acronym'],
          'byline' => $item['byline'],
          'street' => $item['street'],
          'zip' => $item['zip'],
          'city' => $item['city'],
        ]);
      }

      if ($item['state_id'] != 6)
      {
        $project = Project::create([
          'name' => $item['title'],
          'budget' => $item['total'],
          'company_id' => $company->id,
          'principal_id' => $company->id,
          'archived_at' => $item['state_id'] == 3 ? now() : null,
          'created_at' => $item['created_at'],
        ]);
      }
    }

  }
}
