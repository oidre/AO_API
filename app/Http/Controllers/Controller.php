<?php

namespace App\Http\Controllers;

use App\Http\Response\FractalResponse;
use League\Fractal\TransformerAbstract;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class Controller extends BaseController
{
    public function flashError($code, $errors)
    {
        return response()->json([
            'error' => [
                'code' => $code,
                'errors' => $errors
            ],
        ], $code);
    }

    public function paginate($request, $data, $perPage = 10)
    {
        $currentPage = Paginator::resolveCurrentPage();
        $col = collect($data);
        $currentPageItems = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $items = new Paginator($currentPageItems, count($col), $perPage);
        $items->setPath($request->url());
        $items->appends($request->all());

        return $items;
    }
}