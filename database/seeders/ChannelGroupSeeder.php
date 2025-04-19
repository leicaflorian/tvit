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
      ["title" => "Pro Tv", "scrapper" => "ProTv"],
      ["title" => "Antena", "scrapper" => "Antena"],
      ["title" => "La7", "scrapper" => "SuperGuidaTv"],
      ["title" => "Pluto Tv", "scrapper" => "PlutoTv"],
      ["title" => "Regionali", "scrapper" => ""],
    ];

    foreach ($groups as $group) {
      $slug = str_replace(" ", "", strtolower($group["title"]));

      $data = [
        "name"     => $group["title"] ?? '',
        "slug"     => strval($slug) ?? '',
        "scrapper" => $group["scrapper"] ?? '',
      ];

      ChannelGroup::updateOrCreate([
        "slug" => $slug
      ], $data);
    }
  }
}
