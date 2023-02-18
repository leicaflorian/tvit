<?php

namespace Database\Seeders;

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
      
      $newGroup = [
        "name"     => $group,
        "slug"     => $slug,
        "scrapper" => $group["scrapper"],
      ];
      
      \App\Models\ChannelGroup::updateOrCreate([
        "slug" => $slug
      ], $newGroup);;
    }
  }
}
