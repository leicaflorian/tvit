<?php

namespace App\Jobs;

use App\Models\Program;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\ContentLengthException;
use PHPHtmlParser\Exceptions\LogicalException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;
use Psr\Http\Client\ClientExceptionInterface;
use simplehtmldom\HtmlWeb;

class ScrapProgramDetail implements ShouldQueue {
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
  
  public Program $program;
  
  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct(Program $program = null) {
    $this->program = $program;
//    $this->program = Program::find(5108);
  }
  
  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle() {
    $program    = $this->program; //Program::findOrFail($this->program->id);
    $httpClient = new HtmlWeb();
    
    try {
      $dom = $httpClient->load($program->link);

      if ( !$dom) {
        throw new \Exception("Unable to load program detail page " . $program->link);
      }
      
      $coverEl       = $dom->find(".sgtv-aspect-video img", 0) ?: $dom->find("meta[property=og:image]", 0);
      $descriptionEl = $dom->find("p.sgtv-text-justify", 0);
      
      $coverImg    = $coverEl ? ($coverEl->getAttribute("src") ?: $coverEl->getAttribute("content")) : null;
      $description = $descriptionEl ? html_entity_decode(trim($descriptionEl->text()), ENT_QUOTES | ENT_HTML5, "UTF-8") : null;
      
      $program->cover_img   = $coverImg;
      $program->description = $description;
      
      Log::info("Program \"" . $program->title . '" on "' . $program->channel_tvg_slug . "\" updated");
      
      $program->save();
    } catch (ChildNotFoundException|CircularException|ContentLengthException|LogicalException|StrictException|ClientExceptionInterface|NotLoadedException $e) {
      dump($e);
      throw $e;
    }
  }
}
