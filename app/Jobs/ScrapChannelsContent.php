<?php

namespace App\Jobs;

use App\Models\Channel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScrapChannelsContent implements ShouldQueue {
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
  
  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct() {
    dump("Dispatching ScrapChannelsContent job");
    //
  }
  
  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle() {
    $channels = Channel::all();
    
    dump("Starting to scrap " . $channels->count() . " channels");
    
    return;
    foreach ($channels as $key => $channel) {
      $i = $key + 1;
      dump("- Dispatching channel scrapping {$channel->name} ({$i}/{$channels->count()})");
      
      ScrapSingleChannel::dispatch($channel)->onQueue("scrap-channels")->delay(now()->addSeconds(rand(1, 10)));
    }
  }
}
