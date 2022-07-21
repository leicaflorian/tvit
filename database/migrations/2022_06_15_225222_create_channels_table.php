<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('channels', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('page_url');
      $table->integer('channel_number');
      $table->string('logo_url_color');
      $table->string('logo_url_light');
      $table->string('type');
      $table->timestamps();
    });
  }
  
  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('channels');
  }
};
