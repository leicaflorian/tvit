<?php

namespace App\Http\Controllers;

use Adrianorosa\GeoLocation\GeoLocation;
use App\Traits\WebserverOneController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Options;

class RaiController extends Controller {
  use \App\Traits\ChannelController, WebserverOneController {
    stream as public traitStream;
  }
  
  
  private function channelName(): string {
    return "rai";
  }
  
  public function stream($channel) {
    $userIp  = request()->ip();
    $details = GeoLocation::lookup($userIp);
    
    if ($details->getCountryCode() === "IT") {
      return redirect()->action([RaiPlayController::class, 'stream']);
    }
    
    return $this->traitStream($channel);
  }
  
}
