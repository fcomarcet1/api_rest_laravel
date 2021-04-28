<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


/**
 * Class RegisterController
 * @package App\Http\Controllers\Auth
 */
class RegisterController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }


    public function register(Request $request): JsonResponse
    {
        /**
         * Get data user from POST request
         */

        // example json string received from frontend
        // "json={"id":1,"name":"Francisco","surname":"Prieto","role":"ROLE_USER","email":"elisa.prieto.melero@gmail.com","password":"a48330190Z*","description":"","image":""}"

        $requestData = $request->all(); // returns array ['json' => '{"name":"Pepe"...}'].
        $json = $request->input('json'); // returns string '{"name":"Seba",...}'

        $params = json_decode($json); // returns object var_dump($params->name);
        $params_array = json_decode($json, true); // returns  associative array
        $data = [];
       /* dump($params_array);
        die();*/

        if (!empty($params) && !empty($params_array)){

            // Trim data for erase spaces
            $params_array = array_map('trim', $params_array);

            /**
             * Validate data
             * Use validator Library facades
             */
            $validate = Validator::make($params_array, [
                'name'    => 'required|string|alpha|between:2,100',
                'surname' => 'required|string|between:2,100',
                'email'   => 'required|string|email|max:100|unique:users',
                'password'=> 'required|string|min:6'

            ]);

            // Check array validation
            if ($validate->fails()){
                $data = [
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'ERROR. El usuario no se ha creado',
                    'errors' => $validate->errors()
                ];
            }
            else{ // Validation OK

                // Cifrar password.
                $password = password_hash($params->password, PASSWORD_BCRYPT, ['cost'=>4]);

                // Create User.
                $user = new User();
                $user->name = $params_array['name'];
                $user->surname = $params_array['surname'];
                $user->email = $params_array['email'];
                $user->password = $password;
                $user->role = "ROLE_USER";
                $user->updated_at = null;
                // var_dump($user); die();

                // Save user in DB
                $user_saved = $user->save();
                if(!$user_saved){
                    $data['message'] = 'ERROR. No se ha podido guardar el usuario';
                }
                //dump($data); die();
                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El usuario se ha creado correctamente',
                ];
            }
        }
        else { // Empty data from request return error
            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => 'ERROR. Los datos enviados no son correctos',
            ];
        }

        return response()->json($data, $data['code']);

    }

    public function testRegister(Request $request): JsonResponse
    {
        // json={"id":1,"name":"Francisco","surname":"Prieto","role":"ROLE_USER","email":"elisa.prieto.melero@gmail.com","password":"a48330190Z*","description":"","image":""}

        $json = $request->input('json'); // returns string '{"name":"Seba",...}'
        $params = json_decode($json); // returns object var_dump($params->name);
        $params_array = json_decode($json, true); // returns  associative array

        dump($request->input('json'));
        die();

        /** @var User $user */
        $user = User::create([
            'name' => $request->get('name'),
            'surname' => $request->get('surname'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'role' => 'ROLE_USERS',
            'description' => null,
            'image' => null,
            'updated_at' => null,
        ]);

        $data = [
            'status' => 'success',
            'code' => 200,
            'message' => 'El usuario se ha creado correctamente1',
        ];

        return response()->json($data, $data['code']);
    }
}
