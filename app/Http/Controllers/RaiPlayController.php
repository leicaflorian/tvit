<?php

namespace App\Http\Controllers;

use App\Enums\ChannelGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RaiPlayController extends Controller {
  use \App\Traits\ChannelController;
  
  // the relinker answers 403 to requests without a browser-like user agent
  private const USER_AGENT = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0 Safari/537.36";
  
  protected array $localMap = [
    [
      "id"         => "rai-1",
      "iptv_code"  => "2606803",
      "name"       => "Rai 1",
    ],
    [
      "id"         => "rai-2",
      "iptv_code"  => "308718",
      "name"       => "Rai 2",
    ],
    [
      "id"         => "rai-3",
      "iptv_code"  => "308709",
      "name"       => "Rai 3",
    ],
    [
      "id"         => "rai-4",
      "iptv_code"  => "746966",
      "name"       => "Rai 4",
    ],
    [
      "id"         => "rai-movie",
      "iptv_code"  => "747002",
      "name"       => "Rai Movie",
    ],
    [
      "id"         => "rai-5",
      "iptv_code"  => "395276",
      "name"       => "Rai 5",
    ],
    [
      "id"         => "rai-gulp",
      "iptv_code"  => "746953",
      "name"       => "Rai Gulp",
    ],
    [
      "id"         => "rai-yoyo",
      "iptv_code"  => "746899",
      "name"       => "Rai Yoyo",
    ],
  ];
  
  private function baseUrl(): string {
    return "https://mediapolis.rai.it/relinker/relinkerServlet.htm?cont=#channel&output=12";
  }
  
  private function channelName(): string {
    return "rai";
  }
  
  public function stream($channel) {
    $link = $this->getChannelStreamLink($channel);
    
    /*
     * The relinker answers with a redirect to the CDN playlist (with a short-lived token).
     * It sends no CORS headers, so browsers can't follow it: read the redirect here and send
     * the client straight to the CDN, which does allow cross-origin requests.
     */
    $result   = Http::withHeaders(["User-Agent" => self::USER_AGENT])->withoutRedirecting()->get($link);
    $location = $result->header("Location");
    
    if ( !$result->redirect() || !$location) {
      Log::error("RaiPlay relinker failed for {$channel}: HTTP {$result->status()}");
      
      abort(Response::HTTP_BAD_GATEWAY);
    }
    
    // Outside Italy the relinker points to a "content not available" clip instead of a playlist
    if ( !str_contains(parse_url($location, PHP_URL_PATH) ?? "", ".m3u8")) {
      return response()->json([
        "error"   => "geo_blocked",
        "message" => "Questo canale è disponibile solo dall'Italia",
      ], Response::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS);
    }
    
    return redirect($location);
  }
  
  public function chunks($channel, $page) {
    $link = "https://8e7439fdb1694c8da3a0fd63e4dda518.msvdn.net/raiuno1/hls" . "/" . $page;
    
    $result = Http::get($link);
    
    if ($result->ok()) {
      $data = $result->body();

//      return response($data)->header("Content-Type", "video/MP2T");
    }
//  }
  
  }
}
