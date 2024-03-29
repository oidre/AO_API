<?php

namespace App\Http\Controllers;

use App\Project;
use App\Date;
use App\Report;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Transformer\ProjectTransformer;
use App\Transformer\ModuleTransformer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

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
        $this->moduleTransformer = new ModuleTransformer();
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
        if ($validator->fails()) return $this->flashError(422, $validator->errors());

        $res = DB::transaction(function() {
            $project = $this->model->create($this->request->only('name'));
            $data = app('fractal')->item($project, $this->transformer);

            // get current month
            $date = Date::orderBy('id', 'desc')->first();

            // store Report
            $this->storeReport($project->id, $date->id, $this->request->modules, $this->request->application_object_used);
            return [
                'data' => $data, 
                'project' => $project
            ];
        });

        return response()->json($res['data'], 201, [
            'Location' => route('projects.show', ['id' => $res['project']->id]),
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
        if ($validator->fails()) return $this->flashError(422, $validator->errors());

        DB::transaction(function () use ($project, $id) {
            $project->fill($this->request->only('name'));
            $data = $project->save();

            // get current month
            $date = Date::orderBy('id', 'desc')->first();

            // set null if no module deleted
            if ($this->request->deleted_modules == null) {
                $this->request->deleted_modules = [];
            }

            // store report
            $this->storeReport($project->id, $date->id, $this->request->modules, $this->request->application_object_used, $this->request->deleted_modules);
            return $data;
        });

        $project = $this->model->findOrFail($id);

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

    /**
     * GET /projects/{$id}/modules
     * 
     * @param integer $id
     * @return array
     */
    public function modules($id)
    {
        // check if project id exist
        $this->model->findOrFail($id);

        // get latest month
        $date = Date::orderBy('id', 'desc')->first();

        $projects = $this->model
            ->with(['reports' => function ($q) use ($date) {
                $q->where('date_id', $date->id);
            }], 'reports.project')
            ->where('projects.id', $id)
            ->first();
        $modules = [];

        foreach ($projects->reports as $report) {
            array_push($modules, $report->module);
        }
        $paginator = $this->paginate($this->request, $modules);
        $modules = $paginator->getCollection();

        return app('fractal')->paginate($modules, $this->moduleTransformer, $paginator);
    }

    /**
     * GET /projects/{$id}/sum
     * 
     * @param integer $id
     * @return sum of application object
     */
    public function sum($id)
    {
        // check if project id exist
        $this->model->findOrFail($id);

        // get latest month
        $date = Date::orderBy('id', 'desc')->first();

        $sum = Report::where('project_id', $id)->where('date_id', $date->id)->sum('application_object_used');
        return response()->json([
            'data' => [
                'project_id' => $id,
                'sum_of_application_object' => $sum
            ]
        ]);
    }

    /**
     * GET /projects/sum
     * 
     * @return sum all
     */
    public function sumAll()
    {
        // get latest month
        $date = Date::orderBy('id', 'desc')->first();

        $sum = Report::where('date_id', $date->id)->sum('application_object_used');
        return response()->json([
            'data' => [
                'sum_all' => $sum
            ]
        ]);
    }

    private function storeReport($project_id, $date_id, array $modules, array $aou, array $deletedModules = []) {        
        foreach ($modules as $i => $module_id) {
            $report = Report::where('project_id', $project_id)->where('module_id', $module_id)->where('date_id', $date_id)->first();
            if ($report) {
                // found and do update
                $report->application_object_used = $aou[$i];
                $report->save();
            } else {
                // not found create new one
                Report::create([
                    'module_id' => $module_id,
                    'project_id' => $project_id,
                    'date_id' => $date_id,
                    'application_object_used' => $aou[$i],
                ]);
            }
        }

        // deleting modules unused
        if (! empty($deletedModules)) { 
            Report::whereIn('module_id', $deletedModules)->where('date_id', $date_id)->delete();
        }
        
        return;
    }
}
