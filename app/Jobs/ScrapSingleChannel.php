<?php

namespace App\Jobs;

use App\Models\Channel;
use App\Models\Program;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PHPHtmlParser\Dom;
use Psr\Http\Client\ClientExceptionInterface;
use function MongoDB\BSON\toJSON;

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
  public function __construct(Channel $channel) {
    $this->channel = $channel;
  }
  
  /**
   * Execute the job.
   *
   * @return void
   * @throws ClientExceptionInterface|Exception
   */
  public function handle(): void {
    $dom = new Dom;
    $dom->loadFromUrl($this->channel->page_url);
    
    $timelineEntries = $dom->find(".gtv-mod21 .gtv-program");
    $programs        = [];
    
    foreach ($timelineEntries as $timelineEntry) {
      $categoryEl    = $timelineEntry->find(".gtv-program-image .gtv-program-label");
      $thumbnailEl   = $timelineEntry->find(".gtv-program-image img");
      $descriptionEl = $timelineEntry->find(".gtv-program-abstract");
      
      $newProgram = new Program([
        "channel_id"  => $this->channel->id,
        "start"       => date("Y-m-d H:i:s", $timelineEntry->find(".gtv-program-time")->getAttribute("data-start-ts")),
        "end"         => date("Y-m-d H:i:s", $timelineEntry->find(".gtv-program-time")->getAttribute("data-end-ts")),
        "title"       => $timelineEntry->find(".gtv-program-header .gtv-program-title")->innerText,
        "description" => $descriptionEl->count() ? $descriptionEl->innerText : '',
        "category"    => $categoryEl->count() ? $categoryEl->innerText : "",
        "link"        => $timelineEntry->find("a")[0]->getAttribute("href"),
        "thumbnail"   => $thumbnailEl->count() ? $thumbnailEl[0]->getAttribute("src") : '',
        "prima_tv"    => count($timelineEntry->find(".gtv-program-moreinfo .gtv-program-primatv")) > 0,
      ]);
      
      try {
        Program::updateOrCreate([
          'start'      => $newProgram->start,
          'end'        => $newProgram->end,
          'channel_id' => $this->channel->id,
        ], $newProgram->toArray());
      } catch (Exception $e) {
        dump($e->getMessage());
      }
    }
    
    
  }
}
