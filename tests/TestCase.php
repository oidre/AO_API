<?php

abstract class TestCase extends Laravel\Lumen\Testing\TestCase {
  /**
   * Creates the application.
   *
   * @return \Laravel\Lumen\Application
   */
  public function createApplication() {
    return require __DIR__ . '/../bootstrap/app.php';
  }

  protected function apiAs($user, $method, $uri, array $data = [], array $headers = []) {
    $headers = array_merge([
      'Authorization' => 'Bearer ' . app('auth')->login($user),
    ], $headers);

    return $this->api($method, $uri, $data, $headers);
  }

  protected function api($method, $uri, array $data = [], array $headers = []) {
    return $this->json($method, $uri, $data, $headers);
  }

  protected function invalidToken($method, $uri, array $data = [], array $headers = []) {
    $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXV0aFwvbG9naW4iLCJpYXQiOjE1NjY1NDQ3MjgsImV4cCI6MTU2NjU0ODMyOCwibmJmIjoxNTY2NTQ0NzI4LCJqdGkiOiJYRlFiNE1aeFpmbkFiT1pPIiwic3ViIjoxLCJwcnYiOiI4N2UwYWYxZWY5ZmQxNTgxMmZkZWM5NzE1M2ExNGUwYjA0NzU0NmFhIn0.RPZ91Ert7EjAeQGamzAWE-Mh_8D34A0Dl5bzh2BU9_oz';
    $headers = array_merge([
      'Authorization' => 'Bearer ' . $token,
    ], $headers);
    return $this->json($method, $uri, $data, $headers);
  }

  protected function expiredToken($method, $uri, array $data = [], array $headers = []) {
    $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXV0aFwvbG9naW4iLCJpYXQiOjE1NjY1NDU5OTAsImV4cCI6MTU2NjU0NjA1MCwibmJmIjoxNTY2NTQ1OTkwLCJqdGkiOiJEWHduNllBZjV2WkFxOXVQIiwic3ViIjoxLCJwcnYiOiI4N2UwYWYxZWY5ZmQxNTgxMmZkZWM5NzE1M2ExNGUwYjA0NzU0NmFhIn0.-JGcFAlYI1bTiauUrL2NjCBv8LljYnHMeMMAAs8omwg';
    $headers = array_merge([
      'Authorization' => 'Bearer ' . $token,
    ], $headers);
    return $this->json($method, $uri, $data, $headers);
  }

  protected function seeHasHeader($header) {
    $this->assertTrue(
      $this->response->headers->has($header),
      "Response should have the header '{$header}' but does not."
    );

    return $this;
  }

  public function seeHeaderWithRegExp($header, $regexp) {
    $this->seeHasHeader($header)
      ->assertRegExp(
        $regexp,
        $this->response->headers->get($header)
      );

    return $this;
  }
}
