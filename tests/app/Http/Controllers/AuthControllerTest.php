<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class AuthControllerTest extends TestCase {
  use DatabaseMigrations;

  private $user;

  /**
   * Setting up and Create user from factory.
   *
   * @return void
   */
  public function setUp(): void {
    parent::setUp();
    $this->user = factory(App\User::class)->create();
  }

  /**
   * Auth Login Test
   *
   * POST /auth/login
   */

  /** @test */
  public function auth_login_should_fail_with_wrong_password() {
    $this->post(route('auth.login'), [
      'email' => $this->user->email,
      'password' => 'this is invalid password',
    ])
      ->seeStatusCode(400)
      ->seeJson([
        'error' => [
          'code' => 400,
          'message' => 'Invalid credentials',
        ],
      ]);
  }

  /** @test */
  public function auth_login_should_pass_when_email_and_password_match() {
    $this->post(route('auth.login'), [
      'email' => $this->user->email,
      'password' => 'password',
    ])
      ->seeStatusCode(200)
      ->seeJsonStructure([
        'access_token', 'token_type', 'expires_in',
      ]);
  }

  /**
   * Auth Self Test
   *
   * GET /auth/self
   */

  /** @test */
  public function auth_self_should_show_current_authenticated_user() {
    $this->apiAs($this->user, 'get', route('auth.self'))
      ->seeStatusCode(200)
      ->seeJsonStructure([
        'data' => [
          'id', 'name', 'email',
        ],
      ]);
  }

  /** @test */
  public function auth_self_should_fail_when_token_invalid() {
    $this->invalidToken('get', route('auth.self'))
      ->seeStatusCode(401)
      ->seeJson([
        'error' => [
          'code' => 401,
          'message' => 'Token invalid',
        ],
      ]);
  }

  /** @test */
  public function auth_self_should_fail_when_token_expired() {
    $this->expiredToken('get', route('auth.self'))
      ->seeStatusCode(401)
      ->seeJson([
        'error' => [
          'code' => 401,
          'message' => 'Token expired',
        ],
      ]);
  }

  /** @test */
  public function auth_self_should_fail_when_token_absent() {
    $this->json('get', route('auth.self'))
      ->seeStatusCode(401)
      ->seeJson([
        'error' => [
          'code' => 401,
          'message' => 'Token absent',
        ],
      ]);
  }

  /**
   * Auth Refresh Test
   *
   * GET /auth/refresh
   */

  /** @test */
  public function auth_refresh_should_passes_with_valid_token() {
    $this->apiAs($this->user, 'get', route('auth.refresh'))
      ->seeStatusCode(200)
      ->seeJsonStructure([
        'access_token', 'token_type', 'expires_in',
      ]);
  }

  /** @test */
  public function auth_refresh_should_passes_when_token_expired() {
    $this->expiredToken('get', route('auth.refresh'))
      ->seeStatusCode(200)
      ->seeJsonStructure([
        'access_token', 'token_type', 'expires_in',
      ]);
  }

  /** @test */
  public function auth_refresh_should_fail_when_token_invalid() {
    $this->invalidToken('get', route('auth.refresh'))
      ->seeStatusCode(401)
      ->seeJson([
        'error' => [
          'code' => 401,
          'message' => 'Token invalid',
        ],
      ]);
  }

  /** @test */
  public function auth_refresh_should_fail_when_token_absent() {
    $this->json('get', route('auth.refresh'))
      ->seeStatusCode(401)
      ->seeJson([
        'error' => [
          'code' => 401,
          'message' => 'Token absent',
        ],
      ]);
  }

  /**
   * Auth Logout Test
   *
   * POST /auth/logout
   */

  /** @test */
  public function auth_logout_should_passes_with_valid_token() {
    $this->apiAs($this->user, 'post', route('auth.logout'))
      ->seeStatusCode(200)
      ->seeJson([
        'message' => 'Successfully logged out',
      ]);
  }

  /** @test */
  public function auth_logout_should_fail_when_token_expired() {
    $this->expiredToken('post', route('auth.logout'))
      ->seeStatusCode(401)
      ->seeJson([
        'error' => [
          'code' => 401,
          'message' => 'Token expired',
        ],
      ]);
  }

  /** @test */
  public function auth_logout_should_fail_when_token_invalid() {
    $this->invalidToken('post', route('auth.logout'))
      ->seeStatusCode(401)
      ->seeJson([
        'error' => [
          'code' => 401,
          'message' => 'Token invalid',
        ],
      ]);
  }

  /** @test */
  public function auth_logout_should_fail_when_token_absent() {
    $this->json('post', route('auth.logout'))
      ->seeStatusCode(401)
      ->seeJson([
        'error' => [
          'code' => 401,
          'message' => 'Token absent',
        ],
      ]);
  }
}
