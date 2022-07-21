<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string $channel_id
 * @property string $start
 * @property string $end
 * @property string $title
 * @property string $description
 * @property string $category
 * @property string $link
 * @property string $thumbnail
 * @property string $prima_tv
 */
class Program extends Model {
  use HasFactory;
  
  protected $fillable = [
    "channel_id",
    "start",
    "end",
    "title",
    "description",
    "category",
    "link",
    "thumbnail",
    "prima_tv",
  ];
  
  public function channel() {
    return $this->belongsTo(Channel::class);
  }
}
