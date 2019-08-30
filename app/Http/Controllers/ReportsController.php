<?php

namespace App\Http\Controllers;

use App\Report;
use Illuminate\Http\Request;
use Unlu\Laravel\Api\QueryBuilder;
use App\Query\ReportQueryBuilder;
use App\Transformer\ReportTransformer;

class ReportsController extends Controller
{
    private $request;
    private $model;
    private $relationshipMethods = ["date"];

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->model = new Report;
        $this->middleware('auth');
        $this->transformer = new ReportTransformer();
    }

    /**
     * GET /modules
     * 
     * @return array
     */
    public function index()
    {
        $queryBuilder = new ReportQueryBuilder($this->model, $this->request);

        $paginator = $queryBuilder->build()->paginate();
        $reports = $paginator->getCollection();

        return app('fractal')->paginate($reports, $this->transformer, $paginator);
    
        // return response()->json([
        //     'data' => $queryBuilder->build()->paginate(),
        // ]);
    }
}
