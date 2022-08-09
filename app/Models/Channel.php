<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * @property int         $id
 * @property string      $name
 * @property string      $tvg_slug
 * @property string      $tvg_name
 * @property string      $tvg_code
 * @property string      $iptv_code
 * @property string      $logo_url_color
 * @property string      $logo_url_light
 * @property int         $dtt_num
 * @property string      $group
 * @property-read string $m3u8Link
 * @property-read string $rawGuideLink
 *
 * @mixin Builder
 */
class Channel extends Model {
  use HasFactory;
  
  protected $fillable = [
    'name',
    'tvg_slug',
    'tvg_name',
    'tvg_code',
    'iptv_code',
    'logo_url_color',
    'logo_url_light',
    'dtt_num',
    'group',
  ];
  
  
  protected function m3u8Link(): Attribute {
    return Attribute::make(
      get: fn($value) => env("APP_URL") . "/{$this->group}/{$this->tvg_slug}/stream.m3u8",
    );
  }
  
  protected function rawGuideLink(): Attribute {
    return Attribute::make(
      get: fn($value) => "https://www.superguidatv.it/programmazione-canale/oggi/guida-programmi-tv-{$this->tvg_slug}/{$this->tvg_code}/",
    );
  }
  
  protected function nowOnAir(): Attribute {
    return Attribute::make(
      get: fn() => $this->programs()->where("start", "<=", now())->where("end", ">=", now())->first(),
    );
  }
  
  protected function nextOnAir(): Attribute {
    
    
    return Attribute::make(
      get: function ($value) {
        /**
         * @var Program $onAir
         */
        $onAir = $this->nowOnAir;
        
        return $this->programs()->where("start", $onAir->end->setTimezone("utc"))->first();
      },
    );
  }
  
  
  /**
   * @return HasMany
   */
  public
  function programs(): HasMany {
    return $this->hasMany(Program::class, 'channel_tvg_slug', 'tvg_slug');
  }
}
