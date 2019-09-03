<?php

namespace App\Http\Controllers;

use App\Module;
use App\Date;
use Illuminate\Http\Request;
use App\Transformer\ModuleTransformer;
use App\Transformer\ProjectTransformer;
use Illuminate\Support\Facades\Validator;

class ModulesController extends Controller
{
    private $request;
    private $model;
    private $moduleTransformer;
    private $modules;

    /**
     * Create a new ModuleController instance.
     * 
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->model = new Module;
        $this->moduleTransformer = new ModuleTransformer();
        $this->projectTransformer = new ProjectTransformer();
        $this->middleware('auth');
    }

    /**
     * GET /modules
     * 
     * @return array
     */
    public function index()
    {
        $paginator = $this->model->paginate(10);
        $modules = $paginator->getCollection();

        return app('fractal')->paginate($modules, $this->moduleTransformer, $paginator);
    }

    /**
     * GET /modules/{$id}
     * 
     * @param integer $id
     * @return mixed
     */
    public function show($id)
    { 
        return app('fractal')->item($this->model->findOrFail($id), $this->moduleTransformer);
    }

    /**
     * POST /modules
     * 
     * @return mixed
     */
    public function store()
    {
        $validator = Validator::make($this->request->all(), $this->model::$rules);
        if ($validator->fails()) return $this->flashError(422, $validator->errors());

        $module = $this->model->create($this->request->only('name', 'application_object'));
        $data = app('fractal')->item($module, $this->moduleTransformer);

        return response()->json($data, 201, [
            'Location' => route('modules.show', ['id' => $module->id]),
        ]);
    }

    /**
     * PUT /modules/{id}
     * 
     * @param integer $id
     * @return mixed
     */
    public function update($id)
    {   
        $module = $this->model->findOrFail($id);
        $validator = Validator::make($this->request->all(), $this->model::$rules);
        if ($validator->fails()) return $this->flashError(422, $validator->errors());

        $module->fill($this->request->only('name', 'application_object'));
        $module->save();
        return app('fractal')->item($module, $this->moduleTransformer);
    }

    /**
     * DELETE /modules/{$id}
     * 
     * @param integer $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $module = $this->model->findOrFail($id);
        $module->delete();

        return response(null, 204);
    }

    /**
     * GET /modules/{$id}/projects
     * 
     * @param integer $id
     * @return array
     */
    public function projects($id)
    {
        // check if module id exist
        $this->model->findOrFail($id);

        // get latest month
        $date = Date::orderBy('id', 'desc')->first();

        $modules = $this->model
            ->with(['reports' => function ($q) use ($date) {
                $q->where('date_id', $date->id);
            }], 'reports.project')
            ->where('modules.id', $id)
            ->first();

        $projects = [];

        foreach ($modules->reports as $report) {
            array_push($projects, $report->project);
        }
        $paginator = $this->paginate($this->request, $projects);
        $projects = $paginator->getCollection();

        return app('fractal')->paginate($projects, $this->projectTransformer, $paginator);
    }
}
