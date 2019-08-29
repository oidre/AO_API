<?php

namespace App\Http\Controllers;

use App\Http\Response\FractalResponse;
use League\Fractal\TransformerAbstract;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    // public function flashError($errors)
    // {
    //     return response()->json([
    //         'error' => [
    //             'code' => 422,
    //             'errors' => $errors
    //         ],
    //     ], 422);
    // }

    public function flashError($code, $errors)
    {
        return response()->json([
            'error' => [
                'code' => $code,
                'errors' => $errors
            ],
        ], $code);
    }
}