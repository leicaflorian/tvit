<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\ChannelGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RegionaliSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $channelsList = [
      ["#" => 900, "title" => "Tele Norba", "link" => "/proxy/video2s976570-2526/stream/playlist_dvr.m3u8", "logo" => "https://portiamovalore.uniba.it/uploads/loghi/Logo%20telenorba_1579780216.png"],
    ];

    foreach ($channelsList as $channel) {
      $slug = Str::slug($channel["title"]);

      $newChannel = [
        'name'           => $channel["title"],
        'tvg_slug'       => $slug,
        'tvg_name'       => $channel['title'],
        'tvg_code'       => $channel['#'],
        'iptv_code'      => Str::match("/(?<=channel\/)\w+/", $channel['link']),
        'logo_url_color' => $channel['logo'],
        'logo_url_light' => $channel['logo'],
        'dtt_num'        => $channel['#'],
        'group'          => \App\Enums\ChannelGroup::REGIONALI,
        'group_id'       => ChannelGroup::where("slug", "regionali")->first()->id,
      ];

      Channel::updateOrCreate([
        "tvg_slug" => $slug
      ], $newChannel);
    }
  }
}
