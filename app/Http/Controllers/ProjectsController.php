<?php

namespace App\Http\Controllers;

use App\Project;
use App\Date;
use App\Report;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Transformer\ProjectTransformer;
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

            // store Date
            $date_id = $this->storeDate();

            // store Report
            $this->storeReport($project->id, $date_id, $this->request->modules, $this->request->application_object_used);
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

    private function storeDate() {
        Carbon::setLocale('id');
    
        $now = Carbon::now();
    
        // Check if this month already exist
        $date = Date::where('month', $now->month)->where('year', $now->year)->first();
    
        if ($date === null) {
          $date = Date::create([
            'full_date' => $now->format('Y-m-d'),
            'month' => $now->month,
            'month_name' => $now->monthName,
            'year' => $now->year,
          ]);
        }

        return $date->id;
    }

    private function storeReport($project_id, $date_id, array $modules, array $aou) {
        foreach ($modules as $key => $value) {
            Report::create([
                'module_id' => $value,
                'project_id' => $project_id,
                'date_id' => $date_id,
                'application_object_used' => $aou[$key],
            ]);
        }
        
        return;
    }
}
