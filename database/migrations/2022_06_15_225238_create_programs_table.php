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
    Schema::create('programs', function (Blueprint $table) {
      $table->id();
      $table->foreignId("channel_id")->constrained();
      $table->timestamp("start");
      $table->timestamp("end");
      $table->string("title");
      $table->longText("description")->nullable();
      $table->string("category")->nullable();
      $table->string("link")->nullable();
      $table->string("thumbnail")->nullable();
      $table->boolean("prima_tv")->nullable();
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
