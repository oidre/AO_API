<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($this->auth->guard($guard)->guest()) {
            $token = substr($request->header('Authorization'), 7);
            if (! $token) {
                return response()->json([
                    'error' => [
                        'code' => 401,
                        'message' => 'Token absent',
                    ],
                ], 401);
            }
            try {
                app('auth')->setToken($token);
                if (! $claim = app('auth')->payload()) {
                    return response()->json([
                        'error' => [
                            'code' => 401,
                            'message' => 'User not found',
                        ],
                    ], 401);
                }
            } catch (TokenExpiredException $e) {
                if ($request->route()[1]['as'] == 'auth.refresh') {
                    return $next($request);
                }
                return response()->json([
                    'error' => [
                        'code' => 401,
                        'message' => 'Token expired',
                    ],
                ], 401);
            } catch (TokenBlacklistedException $e) {
                return response()->json([
                    'error' => [
                        'code' => 401,
                        'message' => 'Token blacklisted',
                    ],
                ], 401);
            } catch (TokenInvalidException $e) {
                return response()->json([
                    'error' => [
                        'code' => 401,
                        'message' => 'Token invalid',
                    ],
                ], 401);
            }
        }

        return $next($request);
    }
}
