<?php

namespace App\Jobs;

use App\Models\Channel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ScrapChannelsContent implements ShouldQueue {
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
  
  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct() {
    Log::info("Dispatching ScrapChannelsContent job");
    dump("Dispatching ScrapChannelsContent job");
    //
  }
  
  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle(): void {
    $channels = Channel::all();
    
    Log::info("Starting to scrap " . $channels->count() . " channels");
    
    foreach ($channels as $key => $channel) {
      $i = $key + 1;
      dump("- Dispatching channel scrapping {$channel->name} ({$i}/{$channels->count()})");
      Log::info("- Dispatching channel scrapping {$channel->name} ({$i}/{$channels->count()})");
      
      ScrapSingleChannel::dispatch($channel)->onQueue("scrap-channels")->delay(now()->addSeconds(rand(1, 10)));
    }
  }
}
