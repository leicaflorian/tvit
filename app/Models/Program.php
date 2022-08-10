<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int        $id
 * @property string     $channel_tvg_slug
 * @property string     $start
 * @property string     $end
 * @property string     $title
 * @property string     $description
 * @property string     $category
 * @property string     $link
 * @property string     cover_img
 * @property-read  int  $duration
 * @property-read  bool $onAir
 *
 * @mixin Builder
 */
class Program extends Model {
  use HasFactory;
  
  protected $fillable = [
    "channel_tvg_slug",
    "start",
    "end",
    "title",
    "description",
    "category",
    "link",
    "cover_img",
  ];
  
  protected $casts = [
    'start' => 'datetime:Y-m-d H:i:s',
    'end'   => 'datetime:Y-m-d H:i:s',
  ];
  
  protected function start(): Attribute {
    return Attribute::make(
      get: fn($value) => Carbon::parse($value)->setTimezone("Europe/Rome"),
      set: fn($value) => Carbon::parse($value)->setTimezone("utc")
    );
  }
  
  protected function end(): Attribute {
    return Attribute::make(
      get: fn($value) => Carbon::parse($value)->setTimezone("Europe/Rome"),
      set: fn($value) => Carbon::parse($value)->setTimezone("utc")
    );
  }
  
  
  protected function cover_img(): Attribute {
    return Attribute::make(
      get: fn($value) => str_starts_with($value, "/") ? "https://www.superguidatv.it{$value}" : $value,
    );
  }
  
  protected function duration(): Attribute {
    return Attribute::make(
      get: fn($value) => $this->start->diffInMinutes($this->end),
    );
  }
  
  protected function onAir(): Attribute {
    return Attribute::make(
      get: fn($value) => $this->end->isFuture() && $this->start->isPast(),
    );
  }
  
  
  /**
   * @return BelongsTo
   */
  public function channel(): BelongsTo {
    return $this->belongsTo(Channel::class, "channel_tvg_slug", "tvg_slug");
  }
}
