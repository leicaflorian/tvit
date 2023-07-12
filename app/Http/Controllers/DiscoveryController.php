<?php

namespace App\Http\Controllers;

use App\Traits\WebserverOneController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DiscoveryController extends Controller {
  use \App\Traits\ChannelController, WebserverOneController;
  
  private function channelName(): string {
    return "discovery";
  }
  
  
  
}
