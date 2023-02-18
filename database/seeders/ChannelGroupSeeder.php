<?php

namespace Database\Seeders;

use App\Models\ChannelGroup;
use Illuminate\Database\Seeder;

class ChannelGroupSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $groups = [
      ["title" => "Rai", "scrapper" => "SuperGuidaTv"],
      ["title" => "Mediaset", "scrapper" => "SuperGuidaTv"],
      ["title" => "Sky", "scrapper" => "SuperGuidaTv"],
      ["title" => "Discovery", "scrapper" => "SuperGuidaTv"],
      ["title" => "Pro Tv", "scrapper" => "SuperGuidaTv"],
      ["title" => "Antena", "scrapper" => "SuperGuidaTv"],
      ["title" => "La7", "scrapper" => "SuperGuidaTv"],
      ["title" => "Pluto Tv", "scrapper" => "PlutoTv"],
    ];
    
    foreach ($groups as $group) {
      $slug = str_replace(" ", "", strtolower($group["title"]));
      
      ChannelGroup::updateOrCreate(
        [
          "slug" => strval($slug)
        ],
        [
          "name"     => $group,
          "slug"     => strval($slug),
          "scrapper" => $group["scrapper"],
        ]
      );
    }
  }
}
