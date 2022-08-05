<?php

namespace App\Http\Controllers;

use App\Traits\WebserverOneController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Options;

class RaiController extends Controller {
  use \App\Traits\ChannelController, WebserverOneController;

  
  private function channelName(): string {
    return "rai";
  }
  
}
