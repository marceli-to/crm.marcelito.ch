<?php
namespace App\Console;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
  /**
   * Define the application's command schedule.
   */
  protected function schedule(Schedule $schedule): void
  {
    // only run this command if the application is in production
    if (app()->environment('production'))
    {
      $schedule->command('submit:application')->everyMinute();
    }
  }

  /**
   * Register the commands for the application.
   */
  protected function commands(): void
  {
    $this->load(__DIR__.'/Commands');
    require base_path('routes/console.php');
  }
}
