<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RaiPlayStreamTest extends TestCase {

  public function test_redirects_to_the_cdn_playlist_from_italy(): void {
    $playlist = "https://raiuno1-live.akamaized.net/hls/live/598308/raiuno1/raiuno1/playlist_ma.m3u8?hdnea=token";

    Http::fake([
      "mediapolis.rai.it/*" => Http::response("", 302, ["Location" => $playlist]),
    ]);

    $this->get("/raiplay/rai-1/stream.m3u8")
      ->assertRedirect($playlist);
  }

  public function test_answers_451_when_the_relinker_geo_blocks_the_request(): void {
    // From abroad the relinker points to a "not available" clip instead of an HLS playlist
    Http::fake([
      "mediapolis.rai.it/*" => Http::response("", 302, ["Location" => "https://download.rai.it/video_no_available.mp4"]),
    ]);

    $this->get("/raiplay/rai-1/stream.m3u8")
      ->assertStatus(451)
      ->assertJson(["error" => "geo_blocked"]);
  }

  public function test_answers_502_when_the_relinker_fails(): void {
    Http::fake([
      "mediapolis.rai.it/*" => Http::response("", 500),
    ]);

    $this->get("/raiplay/rai-1/stream.m3u8")
      ->assertStatus(502);
  }
}
