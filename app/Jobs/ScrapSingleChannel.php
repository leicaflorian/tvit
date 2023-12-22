<?php

namespace App\Jobs;

use App\Models\Channel;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScrapSingleChannel implements ShouldQueue {
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
  
  /**
   * @var Channel
   */
  private Channel $channel;
  
  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct($channel=null) {
//    $this->channel = $channel;
    $this->channel = Channel::find(1);
  }
  
  public function scrapPrograms(string $day) {
  
  }
  
  /**
   * Execute the job.
   *
   * @return void
   * @throws Exception
   */
  public function handle(): void {
    $channel = $this->channel;
    
    // programmi di oggi
    dump("Starting programs from " . $channel->name . " channel");
    
    try {
      $scrapper = $channel->channelGroup->scrapper;
      
      if ( !$scrapper) {
        $scrapper = "SuperGuidaTv";
      }
      
      $class = "\App\Jobs\Scrappers\\$scrapper" . "Scrapper";
      
      $class::scrap($channel);
    } catch (Exception $e) {
      dump($e);
      throw $e;
    }
    
  }
}
