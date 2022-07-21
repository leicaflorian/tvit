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
    //
  }
  
  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle() {
    $channels = Channel::all();
//    $channels = Channel::where("id", 59)->get();
    
    dump("Starting to scrap " . $channels->count() . " channels");
    
    foreach ($channels as $key => $channel) {
      dump("Scrapping channel " . $channel->name . " " . $key + 1 . "/" . $channels->count());
      ScrapSingleChannel::dispatch($channel);
    }
  }
}
