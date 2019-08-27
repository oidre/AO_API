<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class ModulesControllerTest extends TestCase {
  use DatabaseMigrations;

  private $user;
  private $module;

  /**
   * Setting up and Create user from factory.
   *
   * @return void
   */
  public function setUp(): void {
    parent::setUp();
    $this->user = factory(App\User::class)->create();
    $this->module = factory(App\Module::class)->create();
  }

  /** @test */
  public function index_should_show_collection_of_module() {
    $this
      ->apiAs($this->user, 'get', route('modules.index'))
      ->seeStatusCode(200)
      ->seeJsonStructure([
        'data' => [
          '*' => [

          ],
        ],
      ]);
  }

  /** @test */
  public function show_should_return_a_valid_module() {
    $this
      ->apiAs($this->user, 'get', route('modules.show', ['id' => $this->module->id]))
      ->seeStatusCode(200)
      ->seeJson([
        'id' => $this->module->id,
        'name' => $this->module->name,
        'application_object' => $this->module->application_object,
      ]);
  }

  /** @test */
  public function show_should_fail_when_module_id_does_not_exist() {
    $this
      ->apiAs($this->user, 'get', route('modules.show', ['id' => 9999]))
      ->seeStatusCode(404)
      ->seeJson([
        'error' => [
          'code' => 404,
          'message' => 'Not Found',
        ],
      ]);
  }

  /** @test */
  public function show_route_should_not_match_an_invalid_route() {
    $this
      ->apiAs($this->user, 'get', route('modules.show', ['id' => 'this-is-invalid']))
      ->seeStatusCode(404)
      ->seeJson([
        'error' => [
          'code' => 404,
          'message' => 'Not Found',
        ],
      ]);
  }

  /** @test */
  public function store_should_save_new_book_in_the_database() {
    $this
      ->apiAs($this->user, 'post', route('modules.store'), [
        'name' => 'module name',
        'application_object' => 10,
      ], ['Content-Type' => 'application/json'])
      ->seeJsonStructure([
        'data' => [
          'created',
        ],
      ])
      ->seeInDatabase('modules', ['name' => 'module name']);
  }

  /** @test */
  public function store_should_respond_with_a_201_and_location_header_when_successful() {
    $this
      ->apiAs($this->user, 'post', route('modules.store'), [
        'name' => 'module name',
        'application_object' => 10,
      ], ['Content-Type' => 'application/json'])
      ->seeStatusCode(201)
      ->seeHeaderWithRegExp('Location', '#/modules/[\d]+$#');
  }

  /** @test */
  public function update_should_only_change_fillable_fields() {
    $this->notSeeInDatabase('modules', [
      'name' => 'updated name',
    ]);

    $this
      ->apiAs($this->user, 'put', route('modules.update', ['id' => $this->module->id]), [
        'id' => 9999,
        'name' => 'updated name',
        'application_object' => 10,
      ])
      ->seeStatusCode(200)
      ->seeJson([
        'id' => $this->module->id,
        'name' => 'updated name',
        'application_object' => 10,
      ])
      ->seeInDatabase('modules', [
        'name' => 'updated name',
      ]);
  }

  /** @test */
  public function update_should_fail_with_an_invalid_id() {
    $this
      ->apiAs($this->user, 'put', route('modules.update', ['id' => 9999]))
      ->seeStatusCode(404)
      ->seeJson([
        'error' => [
          'code' => 404,
          'message' => 'Not Found',
        ],
      ]);
  }

  /** @test */
  public function update_should_not_match_an_invalid_route() {
    $this
      ->apiAs($this->user, 'put', route('modules.update', ['id' => 'this-is-invalid']))
      ->seeStatusCode(404)
      ->seeJson([
        'error' => [
          'code' => 404,
          'message' => 'Not Found',
        ],
      ]);
  }

  /** @test */
  public function destroy_should_remove_a_valid_module() {
    $this
      ->apiAs($this->user, 'delete', route('modules.destroy', ['id' => $this->module->id]))
      ->seeStatusCode(204)
      ->isEmpty();

    $this->notSeeInDatabase('modules', ['id' => $this->module->id]);
  }

  /** @test */
  public function destroy_should_return_a_404_with_an_invalid_id() {
    $this
      ->apiAs($this->user, 'delete', route('modules.destroy', ['id' => 9999]))
      ->seeStatusCode(404)
      ->seeJson([
        'error' => [
          'code' => 404,
          'message' => 'Not Found',
        ],
      ]);
  }

  /** @test */
  public function destroy_should_not_match_an_invalid_route() {
    $this
      ->apiAs($this->user, 'put', route('modules.destroy', ['id' => 'this-is-invalid']))
      ->seeStatusCode(404)
      ->seeJson([
        'error' => [
          'code' => 404,
          'message' => 'Not Found',
        ],
      ]);
  }
}
