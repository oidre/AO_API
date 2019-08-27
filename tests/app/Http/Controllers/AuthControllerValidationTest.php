<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class AuthControllerValidationTest extends TestCase {
  use DatabaseMigrations;

  /** @test */
  public function it_validates_required_fields_when_login() {
    $this
      ->post(route('auth.login'))
      ->seeStatusCode(422)
      ->seeJson([
        'errors' => [
          'email' => ['The email field is required.'],
          'password' => ['The password field is required.'],
        ],
      ]);
  }
}
