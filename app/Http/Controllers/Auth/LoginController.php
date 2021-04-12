<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Firebase\JWT\JWT;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $JwtAuth = new JwtAuth();

        // Recibir datos de la peticion
        $data = [];
        $requestData = $request->all(); // returns array ['json' => '{"name":"Pepe"...}']
        $json = $request->input('login'); // returns string '{"name":"Seba",...}'
        $params = json_decode($json); // returns object var_dump($params->name);
        $params_array = json_decode($json, true); // returns  associative array


        // validacion de datos
        $validate = Validator::make($params_array, [
            'email'   => 'required|string|email',
            'password'=> 'required|string|'

        ]);

        // Check array validation
        if ($validate->fails()){
            $signUp = [
                'status' => 'error',
                'code' => 401 , //401 Unauthorized
                'message' => 'ERROR. El usuario no se ha podido identificar',
                'errors' => $validate->errors()
            ];

        }
        else { // validation OK

            // devolver token o datos decodificados del token
            $email = $params->email;
            $password = $params->password;

            $signUp = $JwtAuth->signUp($email, $password);

            if (!empty($params->getToken)){
                $signUp = $JwtAuth->signUp($email, $password, true);
            }
        }

        return response()->json($signUp, 200);
    }

}
