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
    Schema::dropIfExists('programs');
    
    Schema::create('programs', function (Blueprint $table) {
      $table->id();
      
      $table->string('channel_tvg_slug');
      $table->foreign('channel_tvg_slug')->references('tvg_slug')->on('channels');
      
      $table->timestamp("start");
      $table->timestamp("end");
      $table->string("title");
      $table->longText("description")->nullable();
      $table->string("category")->nullable();
      $table->string("link")->nullable();
      $table->timestamps();
    });
  }
  
  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('programs');
  }
};
