<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Transformer\UserTransformer;
use App\Http\Response\FractalResponse;

class UsersController extends Controller
{
    private $fractal;

    public function __construct(Request $request, FractalResponse $fractal)
    {
        $this->request = $request;
        $this->middleware('auth');
        $this->fractal = $fractal;
    }

    public function index()
    {
        return app('fractal')->collection(User::all(), new UserTransformer());
    }
}
