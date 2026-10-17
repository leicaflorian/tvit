<?php

namespace App\Jobs;

use App\Models\Program;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CleanOldPrograms implements ShouldQueue {
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
  
  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct() {
    //
  }
  
  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle() {
    $twoDaysAgo = Carbon::now()->subDays(2);
    
    $deleted = Program::where('end', '<', $twoDaysAgo)->delete();
    
    Log::info("CleanOldPrograms: deleted {$deleted} programs ended before {$twoDaysAgo}");
  }
}
