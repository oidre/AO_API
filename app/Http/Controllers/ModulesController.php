<?php

namespace App\Http\Controllers;

use App\Module;
use Illuminate\Http\Request;
use App\Transformer\ModuleTransformer;
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
}
