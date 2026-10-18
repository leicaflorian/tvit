<?php

namespace Tests\Feature;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request as Psr7Request;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class RaiPlayStreamTest extends TestCase {

  private const PLAYLIST = "https://raiuno1-live.akamaized.net/hls/live/598308/raiuno1/raiuno1/playlist_ma.m3u8?hdnea=token";

  private const PROXY = "http://user:secret@proxy.test:7277";

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

  public function test_resolves_the_relinker_through_the_configured_proxy(): void {
    config(["services.rai.relinker_proxy" => self::PROXY]);
    $proxies = $this->fakeRelinker();

    $this->get("/raiplay/rai-1/stream.m3u8")
      ->assertRedirect(self::PLAYLIST);

    $this->assertSame([self::PROXY], $proxies->all());
  }

  public function test_calls_the_relinker_directly_when_the_proxy_is_empty(): void {
    // docker-compose.prod.yml passes an empty string when Coolify doesn't set the variable
    config(["services.rai.relinker_proxy" => ""]);
    $proxies = $this->fakeRelinker();

    $this->get("/raiplay/rai-1/stream.m3u8")
      ->assertRedirect(self::PLAYLIST);

    $this->assertSame([null], $proxies->all());
  }

  public function test_answers_502_when_the_proxy_is_unreachable(): void {
    config(["services.rai.relinker_proxy" => self::PROXY]);
    Log::spy();

    Http::fake(function ($request) {
      throw new ConnectException("cURL error 7: Failed to connect to proxy.test port 7277", new Psr7Request("GET", $request->url()));
    });

    $this->get("/raiplay/rai-1/stream.m3u8")
      ->assertStatus(502);

    $this->assertProxyNotLogged();
  }

  public function test_answers_502_when_the_proxy_rejects_the_credentials(): void {
    // Guzzle reports a 407 on the CONNECT tunnel as a RequestException, not a ConnectException
    config(["services.rai.relinker_proxy" => self::PROXY]);
    Log::spy();

    Http::fake(function ($request) {
      throw new RequestException("cURL error 56: CONNECT tunnel failed, response 407", new Psr7Request("GET", $request->url()), new Psr7Response(407));
    });

    $this->get("/raiplay/rai-1/stream.m3u8")
      ->assertStatus(502);

    $this->assertProxyNotLogged();
  }

  /**
   * The proxy URL carries the password: it must never reach the logs.
   */
  private function assertProxyNotLogged(): void {
    Log::shouldHaveReceived("error")->once()->withArgs(function ($message) {
      return !str_contains($message, "secret") && !str_contains($message, "proxy.test");
    });
  }

  /**
   * Fakes a relinker that redirects to the CDN playlist, recording the proxy option of each request.
   */
  private function fakeRelinker(): Collection {
    $proxies = collect();

    Http::fake(function ($request, $options) use ($proxies) {
      $proxies->push($options["proxy"] ?? null);

      return Http::response("", 302, ["Location" => self::PLAYLIST]);
    });

    return $proxies;
  }
}
