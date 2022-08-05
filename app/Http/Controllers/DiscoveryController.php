<?php

namespace App\Http\Controllers;

use App\Traits\WebserverOneController;
use Illuminate\Http\Request;

class DiscoveryController extends Controller {
  use \App\Traits\ChannelController, WebserverOneController;
  
  private function channelName(): string {
    return "discovery";
  }
}
