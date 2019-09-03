<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transformer\UserTransformer;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private $request;

    /**
     * Create a new AuthController instance.
     * 
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->middleware('auth', ['except' => 'login']);
    }

    /**
     * Get a JWT via given credentials.
     * POST /auth/login
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $validator = Validator::make($this->request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) return $this->flashError(422, $validator->errors());

        $credentials = $this->request->only('email', 'password');

        if (! $token = app('auth')->attempt($credentials))
        {
            return response()->json([
                'error' => [
                    'code' => 400,
                    'message' => 'Invalid credentials'
                ]
            ], 400);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     * GET /auth/self
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function self()
    {
        return app('fractal')->item(app('auth')->user(), new UserTransformer());
    }

    /**
     * Log the user out (Invalidate the token)
     * POST /auth/logout
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        app('auth')->logout();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    /**
     * Refresh a token
     * GET /auth/refresh
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(app('auth')->refresh());
    }

    /**
     * Get the token array structure.
     * 
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => app('auth')->factory()->getTTL() * 60,
        ]);
    }
}
