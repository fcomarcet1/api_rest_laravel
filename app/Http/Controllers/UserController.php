<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * @param Request $request
     * @return string
     */
    public function test(Request $request): string
    {
        return "Metodo de pruebas UserController";
    }

    public function update (Request $request)
    {
        // Get Token from request(Se obtiene del header).
        $token = $request->header('Authorization');

        // Check token is valid
        $JwtAuth = new JwtAuth();
        $checkToken = $JwtAuth->checkToken($token);

        if ($checkToken){
            echo "Login correcto";
        }
        else{
            echo "Login incorrecto";
        }
        die();
    }

}
