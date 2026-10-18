<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IptvM3uListTest extends TestCase {
  use RefreshDatabase;

  public function test_header_points_players_to_the_epg(): void {
    // With url-tvg in the header most players load the guide without the user adding it by hand
    $content = $this->get("/iptv/list.m3u")->assertOk()->getContent();

    $this->assertSame('#EXTM3U url-tvg="' . config("app.app_url") . '/epg/list.xml"', strtok($content, "\n"));
  }
}
