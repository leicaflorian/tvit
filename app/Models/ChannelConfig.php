<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @property string id
 * @property string internalId
 * @property string code
 * @property string name
 * @property string tvgLogo
 * @property string tvgName
 * @property string tvgId
 * @property string groupTitle
 * @property int    dvbNum
 * @property string m3u8Url
 */
class ChannelConfig extends Model {
  use HasFactory;
  
  protected $fillable = [
    "id",
    "internalId",
    "code",
    "name",
    "tvgLogo",
    "tvgLogoLight",
    "tvgName",
    "tvgId",
    "groupTitle",
    "dvbNum",
    "m3u8Url"
  ];
  
  
  /**
   * @param  mixed   $channel
   * @param  string  $version  dark|light
   *
   * @return string
   */
  private static function getLogoUrl(mixed $channel, string $version = 'dark'): string {
    $logo = "https://api.superguidatv.it/v1/channels/#id/logo?width=120&theme={$version}";
    
    if (str_starts_with($channel["tvgLogo"], "http")) {
      return $channel["tvgLogo"];
    }
    
    return str_replace("#id", $channel["tvgLogo"], $logo);
  }
  
  /**
   * Get all of the models from the database.
   *
   * @param  array|string  $columns
   *
   * @return Collection
   */
  public static function all($columns = ['*']): Collection {
    $data = array_merge(...array_values(config('channels')));
    usort($data, function ($item1, $item2) {
      return $item1['dvbNum'] <=> $item2['dvbNum'];
    });
    
    $channels = collect($data);
    
    return $channels->map(function ($channel, $i) {
      $channel["internalId"]   = $channel["id"];
      $channel["id"]           = $i;
      $channel["tvgLogoLight"] = self::getLogoUrl($channel, "light");
      $channel["tvgLogo"]      = self::getLogoUrl($channel);
      $channel["m3u8Url"]      = env("APP_URL") . "/{$channel["groupTitle"]}/{$channel["internalId"]}/stream.m3u8";
      
      return new ChannelConfig($channel);
    });
  }
}
