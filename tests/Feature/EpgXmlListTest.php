<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\ChannelGroup;
use App\Models\Program;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EpgXmlListTest extends TestCase {
  use RefreshDatabase;

  protected function setUp(): void {
    parent::setUp();
    Carbon::setTestNow(Carbon::parse("2026-10-18 10:00:00", "UTC"));

    $group = ChannelGroup::create(["name" => "Rai", "slug" => "rai", "scrapper" => "SuperGuidaTv"]);

    $channel = new Channel([
      "name"           => "Rai 1 HD",
      "tvg_slug"       => "rai-1",
      "tvg_name"       => "Rai 1",
      "tvg_code"       => "217",
      "iptv_code"      => "2606803",
      "logo_url_color" => "",
      "logo_url_light" => "",
      "dtt_num"        => 1,
      "group"          => "rai",
    ]);
    $channel->group_id = $group->id;
    $channel->save();

    Program::create([
      "channel_tvg_slug" => "rai-1",
      "start"            => Carbon::parse("2026-10-18 18:30:00", "UTC"),
      "end"              => Carbon::parse("2026-10-18 20:00:00", "UTC"),
      "title"            => "TG1",
    ]);
  }

  protected function tearDown(): void {
    Carbon::setTestNow();
    parent::tearDown();
  }

  public function test_channel_id_is_the_slug_used_as_tvg_id_in_the_playlist(): void {
    // Players pair playlist and guide by tvg-id first
    $xml = simplexml_load_string($this->get("/epg/list.xml")->assertOk()->getContent());
    $m3u = $this->get("/iptv/list.m3u")->getContent();

    $this->assertSame("rai-1", (string) $xml->channel[0]["id"]);
    $this->assertStringContainsString('tvg-id="rai-1"', $m3u);
  }

  public function test_tvg_name_is_a_display_name_for_players_that_match_by_name(): void {
    $xml = simplexml_load_string($this->get("/epg/list.xml")->getContent());

    $names = array_map("strval", $xml->xpath("/tv/channel[@id='rai-1']/display-name"));

    $this->assertSame(["Rai 1 HD", "Rai 1"], $names);
  }

  public function test_programme_references_the_slug_and_carries_an_explicit_utc_offset(): void {
    $xml = simplexml_load_string($this->get("/epg/list.xml")->getContent());

    $programme = $xml->programme[0];
    $this->assertSame("rai-1", (string) $programme["channel"]);
    $this->assertSame("20261018183000 +0000", (string) $programme["start"]);
    $this->assertSame("20261018200000 +0000", (string) $programme["stop"]);
  }
}
