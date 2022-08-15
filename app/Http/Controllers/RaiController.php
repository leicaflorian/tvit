<?php

namespace App\Http\Controllers;

use Adrianorosa\GeoLocation\GeoLocation;
use App\Traits\WebserverOneController;
use Illuminate\Support\Facades\Log;
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
    $agent   = new Agent();
    
    $browser = $agent->browser();
    
    Log::info("Browser: $browser");
    Log::info("Ip: {$details->getCountryCode()}");
    
    if ($details->getCountryCode() !== "IT" && $browser) {
      return redirect()->action([RaiPlayController::class, 'stream'], ["channel" => $channel]);
    }
    
    return $this->traitStream($channel);
  }
  
}
