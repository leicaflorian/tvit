<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\ChannelGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExtraChannelsSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $list = [
      [
        'name'           => "Pro TV",
        'tvg_slug'       => "protv",
        'tvg_name'       => "protv",
        'tvg_code'       => 0,
        'iptv_code'      => "protv",
        'logo_url_color' => "https://cmero-ott-images-avod.ssl.cdn.cra.cz/rx/23daa37d-7b69-41ab-90ab-383cc67f3c05.png?default=b507a3ad-e712-494d-9667-5274d0c05bca",
        'logo_url_light' => "https://cmero-ott-images-avod.ssl.cdn.cra.cz/rx/23daa37d-7b69-41ab-90ab-383cc67f3c05.png?default=b507a3ad-e712-494d-9667-5274d0c05bca",
        'dtt_num'        => 99,
        'group'          => "protv",
        'group_id'       => ChannelGroup::where("slug", "protv")->first()->id,
      ],
      [
        'name'           => "Antena 1",
        'tvg_slug'       => "antena1",
        'tvg_name'       => "antena1",
        'tvg_code'       => 0,
        'iptv_code'      => "Antena1",
        'logo_url_color' => "https://assets.antenaplay.ro/2022/10/20/6fiwx2Ydv5JBEsCShluH.png",
        'logo_url_light' => "https://assets.antenaplay.ro/2022/10/20/6fiwx2Ydv5JBEsCShluH.png",
        'dtt_num'        => 99,
        'group'          => "antena",
        'group_id'       => ChannelGroup::where("slug", "antena")->first()->id,
      ],
      [
        'name'           => "Antena 3",
        'tvg_slug'       => "antena3",
        'tvg_name'       => "antena3",
        'tvg_code'       => 0,
        'iptv_code'      => "Antena3",
        'logo_url_color' => "https://assets.antenaplay.ro/2022/10/24/HUA4X5c2dFIsScrTa0KZ.png",
        'logo_url_light' => "https://assets.antenaplay.ro/2022/10/24/HUA4X5c2dFIsScrTa0KZ.png",
        'dtt_num'        => 99,
        'group'          => "antena",
        'group_id'       => ChannelGroup::where("slug", "antena")->first()->id,
      ]
    ];
    
    foreach ($list as $channel) {
      Channel::updateOrCreate([
        "tvg_slug" => $channel['tvg_slug']
      ], $channel);
    }
  }
}
