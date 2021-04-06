<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class JwtAuth
{
    protected $key;

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
    public function signUp(string $email, string $password, $getToken = null)
    {
        $signUp = false;
        $data = [];

        // Buscar si existe el usuario con las credenciales
        $user = User::where(['email' => $email,])->first();

        // Validar que existe el email
        if (is_object($user)){
            // Validar hash password.
            if (password_verify($password, $user->password)){
                $signUp = true;
            }
            if($signUp){
                // Generar token con los datos del usuario identificado
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

                // devolver datos decodificados o el token en funcion del param getToken()
                if (is_null($getToken)){
                    $data = $jwt;
                }
                else{
                    $data = $jwtDecoded;
                }

            }
            else{
                $data = [
                    'status' => 'error',
                    'message' => 'ERROR. El password es incorrecto.',
                    'code' => 400,
                ];
            }

        }
        else{
            $data = [
                'status' => 'error',
                'message' => 'ERROR. El Email es incorrecto o no existe.Si no tiene cuenta por favor registrate',
                'code' => 400,
            ];
        }

        return $data;
    }
}
