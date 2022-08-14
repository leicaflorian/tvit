<?php

namespace App\Http\Controllers;

use Adrianorosa\GeoLocation\GeoLocation;
use App\Traits\WebserverOneController;
use Jenssegers\Agent\Agent;


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
    $agent = new Agent();
  
    $browser = $agent->browser();
    
    if ($details->getCountryCode() === "IT" && !$browser) {
      return redirect()->action([RaiPlayController::class, 'stream'], ["channel" => $channel]);
    }
    
    return $this->traitStream($channel);
  }
  
}
