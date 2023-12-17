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
  
  private string $group;
  
  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct($group = null) {
    $msg = "Dispatching ScrapChannelsContent job";
    
    if ($group) {
      $msg .= " for group {$group}";
    }
    
    Log::info($msg);
    dump($msg);
    
    if ($group) {
      $this->group = $group;
    }
  }
  
  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle(): void {
    if ($this->group) {
      $channels = Channel::where("group", $this->group)->get();
    } else {
      $channels = Channel::all();
    }
    
    Log::info("Starting to scrap " . $channels->count() . " channels");
    
    foreach ($channels as $key => $channel) {
      $i = $key + 1;
      dump("- Dispatching channel scrapping {$channel->name} ({$i}/{$channels->count()})");
      Log::info("- Dispatching channel scrapping {$channel->name} ({$i}/{$channels->count()})");
      
      ScrapSingleChannel::dispatch($channel)->onQueue("scrap-channels")->delay(now()->addSeconds(rand(1, 10)));
    }
  }
}
