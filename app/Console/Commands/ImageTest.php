<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Spatie\Image\Image;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Enums\CropPosition;

class ImageTest extends Command
{
  protected $signature = 'image:test';

  public function handle()
  {
    // ask for the file name
    $file = $this->ask('Enter the name of the file you want to load:');

    // check if the file exists in storage/app/private/public/expenses
    if (!\Storage::disk('public')->exists($file)) {
      $this->error('The file does not exist.');
      return;
    }

    $image = Image::load(\Storage::disk('public')->path($file))
      ->crop(250, 250, CropPosition::Center)
      ->save();

    $image->save('public/expenses/' . $file);

    $this->info('The file exists.');

  }
}
