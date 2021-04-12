<?php

namespace App\Http\Middleware;

use App\Helpers\JwtAuth;
use Closure;
use Illuminate\Http\Request;

class ApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /*
         // Get Token from request(Se obtiene del header).
        $token = $request->header('Authorization');
        $secret_key = 'kj43dg67hkñkf745kfj.d363bh9667n%&&3gidbfino$$.j436dghfo6534gfnghio';

        if ($token !== $secret_key) {
            return redirect('home');
        }
        */

        // Comprobar si el usuario esta identificado comprobando el token

        // Get Token from request(Se obtiene del header).
        $token = $request->header('Authorization');

        // Check token is valid
        $JwtAuth = new JwtAuth();
        $checkToken = $JwtAuth->checkToken($token);

        if ($checkToken){
            $respose = $next($request);
        }
        else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => ' El usuario no esta identificado'
            ];

            $respose = response()->json($data, $data['code']);

        }

        return $respose;
    }
}
