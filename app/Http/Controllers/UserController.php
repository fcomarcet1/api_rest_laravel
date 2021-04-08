<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
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

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update (Request $request): JsonResponse
    {
        // Comprobar si el usuario esta identificado comprobando el token

        // Get Token from request(Se obtiene del header).
        $token = $request->header('Authorization');

        // Check token is valid
        $JwtAuth = new JwtAuth();
        $checkToken = $JwtAuth->checkToken($token);

        // Recoger datos por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if ($checkToken && !empty($params_array)) {

            // obtener usuario identificado
            $user = $JwtAuth->checkToken($token, true);

            // Trim data for erase spaces
            $params_array = array_map('trim', $params_array);

            //validar datos
            $validate = Validator::make($params_array,[
                'name'    => 'required|string|alpha|between:2,100',
                'surname' => 'required|string|alpha|between:2,100',
                'email'  => 'required|email|max:100|unique:users,email,'.$user->sub
            ]);

            // Check validate data array
            if ($validate->fails()){
                $data = [
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'ERROR. El usuario no se ha creado',
                    'errors' => $validate->errors()
                ];
            }
            else{
                // Quitar campos que no queremos actualizar
                unset($params_array['id']);
                unset($params_array['role']);
                unset($params_array['password']);
                unset($params_array['created_ad']);
                unset($params_array['remember_token']);

                // Actualizar usuario en bbdd
                $user_update = User::where('id', $user->sub)->update($params_array);

                //devolver array con resultado
                $data = [
                    'status' => 'succes',
                    'code' => 200,
                    'message' => 'Datos de usuario actualizados correctamente.',
                    'user' => $user,
                    'user_update' => $params_array
                ];
            }

        }
        else{ // Error user not logged
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => ' El usuario no esta identificado'
            ];
        }

       return response()->json($data, $data['code']);
    }

}
