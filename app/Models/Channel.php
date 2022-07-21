<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @property int    $id
 * @property string $name
 * @property string $name_slug
 * @property string $page_url
 * @property int    $channel_number
 * @property string $logo_url_color
 * @property string $logo_url_light
 * @property string $type 'free'|'premium'|'sky'
 */
class Channel extends Model {
  use HasFactory;
  
  protected $fillable = ['name',
    'name_slug',
    'page_url',
    'channel_number',
    'logo_url_color',
    'logo_url_light',
    'type'];
  
  
  public function programs(): \Illuminate\Database\Eloquent\Relations\HasMany {
    return $this->hasMany(Program::class);
  }
}
