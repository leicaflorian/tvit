<?php

namespace App\Jobs;

use App\Models\Program;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\ContentLengthException;
use PHPHtmlParser\Exceptions\LogicalException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;
use Psr\Http\Client\ClientExceptionInterface;

class ScrapProgramDetail implements ShouldQueue {
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
  
  public Program $program;
  
  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct(Program $program) {
    $this->program = $program;
  }
  
  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle() {
    $program = $this->program;//Program::findOrFail($this->program->id);
    
    
    try {
      $dom = new Dom();
      $dom->loadFromUrl($program->link);
      
      $coverImg    = $dom->find(".sgtvdetails_imgBackdrop")->getAttribute("src");
      $description = $dom->find(".sgtvdetails_divContainer .sgtvdetails_divTableRow .sgtvdetails_divRowContent .sgtvdetails_divContentText")->text();
      
      $program->cover_img   = $coverImg;
      $program->description = $description;
      
      $program->save();
    } catch (ChildNotFoundException|CircularException|ContentLengthException|LogicalException|StrictException|ClientExceptionInterface|NotLoadedException $e) {
      dump($e);
      throw $e;
    }
  }
}
