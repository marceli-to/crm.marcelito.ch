<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\Timer;

class ImportEntries extends Command
{
  protected $signature = 'import:entries';

  protected $description = 'Loads a csv file from /storage/app/public/';

  public function handle()
  {
    // ask for the file name
    $file = $this->ask('Enter the name of the file you want to load (without the .csv extension):');

    // check if the file exists
    if (!\Storage::disk('public')->exists($file . '.csv')) {
      $this->error('The file does not exist.');
      return;
    }

    // get the contents of the file
    $csvData = \Storage::disk('public')->get($file . '.csv');

    // Split the CSV data into lines
    $lines = explode("\n", $csvData);

    // Remove any empty lines
    $lines = array_filter($lines);

    // Parse each line
    $parsedData = array_map(function ($line) {
      return str_getcsv($line);
    }, $lines);

    // Define the column names
    $columns = ['name', 'email', 'company', 'project', 'empty1', 'task', 'no', 'start_date', 'start_time', 'end_date', 'end_time', 'duration', 'empty2'];

    // Combine column names with values
    $result = array_map(function ($row) use ($columns) {
      $combined = array_combine($columns, $row);
      $combined['duration'] = $this->convertDurationToMinutes($combined['duration']);
      return $combined;
    }, $parsedData);

    foreach ($result as $entry) {
      Timer::create([
        'task' => $entry['task'],
        'date' => $entry['start_date'],
        'time_start' => $entry['start_time'],
        'time_end' => $entry['end_time'],
        'duration' => $entry['duration'],
        'project_id' => $file,
      ]);
    }
  }

  private function convertDurationToMinutes($duration)
  {
    $parts = explode(':', $duration);
    $hours = intval($parts[0]);
    $minutes = intval($parts[1]);
    $seconds = intval($parts[2]);
    return $hours * 60 + $minutes + ceil($seconds / 60);
  }
}
