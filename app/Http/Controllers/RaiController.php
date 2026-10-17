<?php

namespace App\Http\Controllers;

class RaiController extends Controller {
  
  /**
   * Rai channels are served from the official RaiPlay relinker.
   *
   * The previous source (a webserver.one / opposepresent.net restream) is gone,
   * so every client now goes through RaiPlay. RaiPlay streams are geo-blocked
   * outside Italy, so foreign users need an Italian connection.
   *
   * @param  string  $channel
   *
   * @return \Illuminate\Http\RedirectResponse
   */
  public function stream($channel) {
    return redirect()->action([RaiPlayController::class, 'stream'], ["channel" => $channel]);
  }
}
