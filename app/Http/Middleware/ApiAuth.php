<?php

namespace App\Http\Middleware;

use App\Helpers\JwtAuth;
use Closure;
use Exception;
use Illuminate\Http\Request;

class ApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticate with your token

        // Get Token from request(Se obtiene del header).
        $token = $request->header('Authorization');

        // Check token is valid
        $JwtAuth = new JwtAuth();
        $checkToken = $JwtAuth->checkToken($token);

        if ($checkToken){
            $response = $next($request);
        }
        else{
            $data = [
                'code' => 401, //401 Unauthorized
                'status' => 'error',
                'message' => ' El usuario no esta identificado'
            ];

            $response = response()->json($data, $data['code']);

        }

        return $response;
    }
}
