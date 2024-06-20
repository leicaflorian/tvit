<?php

namespace App\Console;

use App\Jobs\ScrapChannelsContent;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {
  /**
   * Define the application's command schedule.
   *
   * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
   *
   * @return void
   */
  protected function schedule(Schedule $schedule) {
    $schedule->job(new ScrapChannelsContent(), "default")
			->dailyAt('3:00')
      ->timezone('Europe/Rome')
			// Update APP_ENV in .env file to production, otherwise will not work
      ->environments(['production']);
  }
  
  /**
   * Register the commands for the application.
   *
   * @return void
   */
  protected function commands() {
    $this->load(__DIR__ . '/Commands');
    
    require base_path('routes/console.php');
  }
}
