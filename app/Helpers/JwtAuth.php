<?php

namespace App\Helpers;

use Exception;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class JwtAuth
{
    /**
     * @var string
     */
    protected $secret_key;
    protected $encrypt;
    private static $aud = null;

    /**
     * JwtAuth constructor.
     */
    public function __construct()
    {
        $this->secret_key = 'kj43dg67hkñkf745kfj.d363bh9667n%&&3gidbfino$$.j436dghfo6534gfnghio';
        $this->encrypt = ['HS256'];
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
                    'role' => $user->role,
                    'aud' => self::Aud(),
                    'iat' => time(),
                    'exp' => time() + (7 * 24 * 60 * 60)
                ];

                // codificar token
                $jwt = JWT::encode($token, $this->secret_key, 'HS256');

                // decodificar token para obtener datos del usuario identificado
                $jwtDecoded = JWT::decode($jwt, $this->secret_key, ['HS256']);

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

    /**
     * @param $jwt
     * @param false $getIdentity
     * @return bool|object
     * @throws Exception
     */
    public function checkToken($jwt, $getIdentity = false)
    {
        $auth = false;

        if(empty($jwt))
        {
            $auth = false;
            //throw new Exception("Invalid token supplied.");
        }

        // decode json && check possible errors
        try {
            // Eliminar comillas doble si llega como string
            $jwt = str_replace('"', '', $jwt);
            $JwtDecoded = JWT::decode($jwt, $this->secret_key, $this->encrypt);
        } catch (\UnexpectedValueException $e) {
            $auth = false;
        } catch (\DomainException $e) {
            $auth = false;
        }

        // Validate token --> is valid && is object && exist id
        if(!empty($JwtDecoded) && is_object($JwtDecoded) && isset($JwtDecoded->sub)){
            $auth = true;
        } else{
            $auth = false;
        }

        // Return decoded token with user information if $getIdentity = true
        if($getIdentity){
            return $JwtDecoded;
        }

        return $auth;
    }


    private static function Aud()
    {
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }
}
