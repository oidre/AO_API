<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class ModulesControllerValidationTest extends TestCase {
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
  public function it_validates_required_fields_when_creating_a_new_module() {
    $this
      ->apiAs($this->user, 'post', route('modules.store'))
      ->seeStatusCode(422)
      ->seeJson([
        'errors' => [
          'name' => ['The name field is required.'],
          'application_object' => ['The application object field is required.'],
        ],
      ]);
  }

  /** @test */
  public function it_validates_required_fields_when_updating_a_module()
  {
    $this
      ->apiAs($this->user, 'put', route('modules.update', ['id' => $this->module->id]))
      ->seeStatusCode(422)
      ->seeJson([
        'errors' => [
          'name' => ['The name field is required.'],
          'application_object' => ['The application object field is required.'],
        ],
      ]);
  }

  /** @test */
  public function it_validates_numeric_fields_on_application_object_when_creating_a_new_module()
  {
    $this
      ->apiAs($this->user, 'post', route('modules.store'), [
        'name' => 'some name',
        'application_object' => 'not numeric'
      ])
      ->seeStatusCode(422)
      ->seeJson([
        'errors' => [
          'application_object' => ['The application object must be a number.'],
        ],
      ]);
  }

  /** @test */
  public function it_validates_numeric_fields_on_application_object_when_updating_a_module()
  {
    $this
      ->apiAs($this->user, 'put', route('modules.update', ['id' => $this->module->id]), [
        'name' => 'some name',
        'application_object' => 'not numeric'
      ])
      ->seeStatusCode(422)
      ->seeJson([
        'errors' => [
          'application_object' => ['The application object must be a number.'],
        ],
      ]);
  }

  /** @test */
  public function it_validates_at_least_0_on_application_object_when_creating_a_new_module()
  {
    $this
      ->apiAs($this->user, 'post', route('modules.store'), [
        'name' => 'some name',
        'application_object' => -10
      ])
      ->seeStatusCode(422)
      ->seeJson([
        'errors' => [
          'application_object' => ['The application object must be at least 0.'],
        ],
      ]);
  }

  /** @test */
  public function it_validates_at_least_0_on_application_object_when_updating_a_module()
  {
    $this
      ->apiAs($this->user, 'put', route('modules.update', ['id' => $this->module->id]), [
        'name' => 'some name',
        'application_object' => -10
      ])
      ->seeStatusCode(422)
      ->seeJson([
        'errors' => [
          'application_object' => ['The application object must be at least 0.'],
        ],
      ]);
  }

  /** @test */
  public function application_object_passes_create_validation_when_exactly_min()
  {
    $this
      ->apiAs($this->user, 'post', route('modules.store'), [
        'name' => 'some name',
        'application_object' => 0
      ])
      ->seeStatusCode(201)
      ->seeHeaderWithRegExp('Location', '#/modules/[\d]+$#');
  }

  /** @test */
  public function application_object_passes_update_validation_when_exactly_min()
  {
    $this
      ->apiAs($this->user, 'put', route('modules.update', ['id' => $this->module->id]), [
        'name' => 'some name',
        'application_object' => 0
      ])
      ->seeStatusCode(200)
      ->seeJson([
        'id' => $this->module->id,
        'name' => 'some name',
        'application_object' => 0
      ]);
  }
}
