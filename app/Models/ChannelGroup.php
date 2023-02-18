<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * * @mixin Builder
 */
class ChannelGroup extends Model {
  use HasFactory;
  
  protected $fillable = [
    "name",
    "slug",
    "scrapper",
  ];
  
  public function channels(): HasMany {
    return $this->hasMany(Channel::class, "group_id");
  }
}
