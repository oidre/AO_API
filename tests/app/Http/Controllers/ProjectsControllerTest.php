<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class ProjectsControllerTest extends TestCase {
  use DatabaseMigrations;

  private $user;
  private $project;

  /**
   * Setting up and Create user from factory.
   *
   * @return void
   */
  public function setUp(): void {
    parent::setUp();
    factory(App\Module::class, 50)->create();
    factory(App\Date::class)->create();
    $this->user = factory(App\User::class)->create();
    $this->project = factory(App\Project::class)->create();
  }

  /** @test */
  public function index_should_show_collection_of_project() {
    $this
      ->apiAs($this->user, 'get', route('projects.index'))
      ->seeStatusCode(200)
      ->seeJsonStructure([
        'data' => [
          '*' => [

          ],
        ],
      ]);
  }

  /** @test */
  public function show_should_return_a_valid_project() {
    $this
      ->apiAs($this->user, 'get', route('projects.show', ['id' => $this->project->id]))
      ->seeStatusCode(200)
      ->seeJson([
        'id' => $this->project->id,
        'name' => $this->project->name,
      ]);
  }

  /** @test */
  public function show_should_fail_when_project_id_does_not_exist() {
    $this
      ->apiAs($this->user, 'get', route('projects.show', ['id' => 9999]))
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
      ->apiAs($this->user, 'get', route('projects.show', ['id' => 'this-is-invalid']))
      ->seeStatusCode(404)
      ->seeJson([
        'error' => [
          'code' => 404,
          'message' => 'Not Found',
        ],
      ]);
  }

  /** @test */
  public function store_should_save_new_project_in_the_database() {
    $this
      ->apiAs($this->user, 'post', route('projects.store'), [
        'name' => 'project name',
        'modules' => [1, 2, 3],
        'application_object_used' => [5, 5, 5],
      ], ['Content-Type' => 'application/json'])
      ->seeJsonStructure([
        'data' => [
          'created',
        ],
      ]);
    
    $data = json_decode($this->response->getContent(), true);
    $this
      ->seeInDatabase('projects', ['name' => $data['data']['name']])
      ->seeInDatabase('reports', ['project_id' => $data['data']['id']]);
  }

  /** @test */
  public function store_should_respond_with_a_201_and_location_header_when_successful() {
    $this
      ->apiAs($this->user, 'post', route('projects.store'), [
        'name' => 'project name',
        'modules' => [1, 2, 3],
        'application_object_used' => [5, 5, 5],
      ], ['Content-Type' => 'application/json'])
    // dd(json_decode($this->response->getContent(), true));
      ->seeStatusCode(201)
      ->seeHeaderWithRegExp('Location', '#/projects/[\d]+$#');
  }

  /** @test */
  public function destroy_should_remove_a_valid_project() {
    $this
      ->apiAs($this->user, 'delete', route('projects.destroy', ['id' => $this->project->id]))
      ->seeStatusCode(204)
      ->isEmpty();

    $this->notSeeInDatabase('projects', ['id' => $this->project->id]);
  }

  /** @test */
  public function destroy_should_return_a_404_with_an_invalid_id() {
    $this
      ->apiAs($this->user, 'delete', route('projects.destroy', ['id' => 9999]))
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
      ->apiAs($this->user, 'put', route('projects.destroy', ['id' => 'this-is-invalid']))
      ->seeStatusCode(404)
      ->seeJson([
        'error' => [
          'code' => 404,
          'message' => 'Not Found',
        ],
      ]);
  }
}
