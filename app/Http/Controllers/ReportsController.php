<?php

namespace App\Http\Controllers;

use App\Report;
use Illuminate\Http\Request;
use Unlu\Laravel\Api\QueryBuilder;

class ReportsController extends Controller
{
    private $request;
    private $model;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->model = new Report;
        $this->middleware('auth');
    }

    /**
     * GET /modules
     * 
     * @return array
     */
    public function index()
    {
        $queryBuilder = new QueryBuilder($this->model, $this->request);
    
        return response()->json([
            'data' => $queryBuilder->build()->paginate(),
        ]);
    }
}
