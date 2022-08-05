<?php

namespace App\Http\Controllers;

use App\Traits\WebserverOneController;
use \App\Traits\ChannelController;
use Illuminate\Http\Request;

class SkyController extends Controller {
  use ChannelController, WebserverOneController;
  
  private function channelName(): string {
    return "sky";
  }
}
