<?php

namespace App\Http\Middleware;

use App\Helpers\JwtAuth;
use Closure;
use Exception;
use Illuminate\Http\Request;

class RoleAdminAuth
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

        // Check token is valid and not empty
        $JwtAuth = new JwtAuth();
        $checkToken = $JwtAuth->checkToken($token);

        // Get role
        $user = $JwtAuth->checkToken($token, true);
        //dump($user); die();
        // $user_id = $user->sub;
        $user_role = $user->role;

        if ($checkToken && $user_role == 'ROLE_ADMIN'){
            $response = $next($request);
        }
        else{
            $data = [
                'code' => 401, //401 Unauthorized
                'status' => 'error',
                'message' => 'ERROR. Usuario no autorizado.'
            ];

            $response = response()->json($data, $data['code']);

        }

        return $response;
    }
}
