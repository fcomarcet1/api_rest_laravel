<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class JwtAuth
{
    public $key;

    /**
     * JwtAuth constructor.
     */
    public function __construct()
    {
        $this->key = 'kj43dg67hkñkf745kfj.d363bh9667n%&&3gidbfino$$.j436dghfo6534gfnghio';
    }

    /**
     * @param String $email
     * @param String $password
     * @param null $getToken
     * @return array|object|string
     */
    public function signUp(String $email, String $password, $getToken = null)
    {
        $signUp = false;
        $data = [];

        // Buscar si existe el usuario con las credenciales
        $user = User::where([
            'email' => $email,
            //'password' => $password NO PUEDO ASI POR EL PASSWORD HASH
        ])->first();


        /*if (!password_verify($password, $user->password)){
            $message = 'ERROR.Password Incorrecto';
        }*/

        // comprobar si las creedenciales son correctas .
        if (is_object($user) && password_verify($password, $user->password)){
            $signUp = true;
        }

        // generar token con los datos del usuario identificado
        if($signUp){
            $token = [
                'sub' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'surname' => $user->surname,
                'iat' => time(),
                'exp' => time() + (7 * 24 * 60 * 60)
            ];

            // codificar token
            $jwt = JWT::encode($token, $this->key, 'HS256');

            // decodificar token para obtener datos del usuario identificado
            $jwtDecoded = JWT::decode($jwt, $this->key, ['HS256']);

            // devolver datos decodificados o el token en funcion de un parametro
            if (is_null($getToken)){
                $data = $jwt;
            }
            else{
                $data = $jwtDecoded;
            }

        }
        else {
           // Error Login.
           $data = [
               'status' => 'error',
               'message' => 'Error. Datos de acceso incorrectos',
               'code' => 400,
           ];
        }

        return $data;
    }


}
