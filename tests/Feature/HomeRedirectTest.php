<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomeRedirectTest extends TestCase {

  public function test_home_redirects_to_the_channels_page(): void {
    $this->get("/")
      ->assertRedirect(route("channels.index"));
  }
}
