<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
     * @throws Exception
     */
    public function update(Request $request): JsonResponse
    {
        // Comprobar si el usuario esta identificado comprobando el token

        // Get Token from request(Se obtiene del header).
        $token = $request->header('Authorization');

        // Check token is valid
        $JwtAuth = new JwtAuth();
        $checkToken = $JwtAuth->checkToken($token); // return true|false

        // Recoger datos por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if ($checkToken && !empty($params_array)) {
            // obtener usuario identificado
            $user = $JwtAuth->checkToken($token, true);
            $user_id = $user->sub;
            $user_email_received = $params_array['email'];

            // Trim data for erase spaces
            $params_array = array_map('trim', $params_array);

            // comprobar si el email pertenece a otro usuario registrado
            $email_find = User::where('email',$user_email_received )->first();
            $email_find_user_logged = User::where('id',$user->sub )->get('email')->first();
            //$exist_email = User::where('id', $user->sub)->get('email')->get();

            // Check if exist email in DB && user is owner of this email
            if ($email_find !== null && ($user->sub !== $email_find->id )){
                echo "error. el email existe en la BD y pertenece a otro usuario";
                //devolver array con resultado
                $data = [
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Datos de usuario actualizados correctamente.',
                ];

            }
            else{
                 //OK.El email no pertenece a otro user y podemos actualizarlo o dejar el mismo;
                //validar datos
                $validate = Validator::make($params_array,[
                    'name'    => 'required|string|alpha|between:2,100',
                    'surname' => 'required|string|between:2,100',
                    'email'  => 'required|email|max:100|',[
                        Rule::unique('users')->ignore($user->sub)
                    ]
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
                        'code' => 201, //201 Created response for PUT
                        'message' => 'Datos de usuario actualizados correctamente.',
                        'user' => $user,
                        'user_updated' => $params_array
                    ];
                }
            }
        }
        else{ // Error user not logged
            $data = [
                'code' => 401, //401 Unauthorized
                'status' => 'error',
                'message' => ' El usuario no esta identificado'
            ];
        }

        return response()->json($data, $data['code']);
    }

}
