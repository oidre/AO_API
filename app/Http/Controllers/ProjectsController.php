<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Http\Request;
use App\Transformer\ProjectTransformer;
use Illuminate\Support\Facades\Validator;

class ProjectsController extends Controller
{
    private $model;
    private $request;
    private $transformer;

    /**
     * Create a new ProjectsController instance.
     * 
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->model = new Project;
        $this->middleware('auth');
        $this->transformer = new ProjectTransformer();
    }

    /**
     * GET /projects
     * 
     * @return array
     */
    public function index()
    {
        $paginator = $this->model->paginate(10);
        $projects = $paginator->getCollection();

        return app('fractal')->paginate($projects, $this->transformer, $paginator);
    }

    /**
     * GET /projects/{$id}
     * 
     * @param integer $id
     * @return mixed
     */
    public function show($id)
    {
        return app('fractal')->item($this->model->findOrFail($id), $this->transformer);
    }

    /**
     * POST /projects
     * 
     * @return mixed
     */
    public function store()
    {
        $validator = Validator::make($this->request->all(), $this->model::$rules);
        if ($validator->fails()) return $this->flashError($validator->errors());

        $project = $this->model->create($this->request->only('name'));
        $data = app('fractal')->item($project, $this->transformer);

        return response()->json($data, 201, [
            'Location' => route('projects.show', ['id' => $project->id]),
        ]);
    }

    /**
     * PUT /projects/{id}
     * 
     * @param integer $id
     * @return mixed
     */
    public function update($id)
    {   
        $project = $this->model->findOrFail($id);
        $validator = Validator::make($this->request->all(), $this->model::$rules);
        if ($validator->fails()) return $this->flashError($validator->errors());

        $project->fill($this->request->only('name'));
        $project->save();
        return app('fractal')->item($project, $this->transformer);
    }

    /**
     * DELETE /projects/{$id}
     * 
     * @param integer $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $project = $this->model->findOrFail($id);
        $project->delete();

        return response(null, 204);
    }
}
