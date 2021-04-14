<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;   // cargamos File para obtener la imagen en su guardado
use Illuminate\Support\Facades\Storage;// cargamos storage para almacenar la imagen
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
     *  Update user profile
     *
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
        //$checkToken = $JwtAuth->checkToken($token); // return true|false

        // Recoger datos por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {

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
                //devolver array con resultado
                $data = [
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'ERROR.No puedes elegir ese email ya pertenece a otro usuario.',
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
                    $params_array['updated_at'] = date("Y-m-d H:m:s");

                    // Quitar campos que no queremos actualizar
                    unset($params_array['id']);
                    unset($params_array['role']);
                    unset($params_array['password']);
                    unset($params_array['created_at']);
                    unset($params_array['remember_token']);

                    // Actualizar usuario en bbdd
                    $user_updated = User::where('id', $user->sub)->update($params_array);

                    if ($user_updated){
                    $data = [
                        'status' => 'succes',
                        'code' => 201, //201 Created response for PUT
                        'user' => $user,
                        'message' => 'Datos de usuario actualizados correctamente.',
                        'user_updated' => $params_array
                        ];
                    }
                    else{
                        $data = [
                            'status' => 'error',
                            'code' => 400,
                            'message' => 'ERROR.No se pudo actualizar los datos de usuario',
                        ];
                    }

                }
            }
        }
        else{ // Error user not logged
            $data = [
                'code' => 401, //401 Unauthorized
                'status' => 'error',
                'message' => ' El usuario no esta identificado',
            ];
        }

        return response()->json($data, $data['code']);
    }


    /**
     * Upload user avatar
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function upload(Request $request): JsonResponse
    {

        // Validate image from request
        $validate = Validator::make($request->all(),[
            'file0' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:20048',
        ]);

        if($validate->fails()) {
            $data = [
                'code' => 500,
                'status' => 'error',
                'message' => ' ERROR. Selecciona una imagen valida',
                'errors' => $validate->errors()
            ];

            return response()->json($data, $data['code']);
        }

        // Determine if a file is present on the request && Check if image has been uploaded
        if ($request->hasFile('file0') && $request->file('file0')->isValid()){

            // Get image from request
            $image = $request->file('file0');

            // Guardar imagen
            if($image){

                $image_path_unique = time().'_'.$image->getClientOriginalName();
                $image_path        = $image->path(); // tmp path
                $image_extension   = $image->extension();
                $mime_type         = $image->getClientMimeType(); // image/jpg
                $image_to_save     = File::get($image);

                //Usamos la clase storage y su metodo estatico disk para almacenar la imagen
                // con el metodo put en storage/users/avatar (Metodo Victor)
                // $image_uploaded_path = Storage::disk('users')->put($image_path_unique, $image_to_save);

                // MAS SENCILLO docs oficial laravel
                $uploadFolder = 'users';
                $uploaded_image = $image->storeAs($uploadFolder, $image_path_unique );

                // Almacenar image_path en BBDD

                // Get Token from request(Se obtiene del header)
                $token = $request->header('Authorization');

                // Get id user logged.
                $JwtAuth = new JwtAuth();
                $user_logged = $JwtAuth->checkToken($token, true);
                $user_id = $user_logged->sub;
                //$user_logged->image = $image_path_unique;

                // find user in DB
                $user_update = User::find($user_id);

                // Set data in user obj for save in DB
                $user_update->image = $image_path_unique;
                $user_update->updated_at = date("Y-m-d H:m:s");

                // Save user in DB
                 $save = $user_update->save();

                if($uploaded_image && $save){
                    $data = [
                        'code' => 200,
                        'status' => 'success',
                        'message' => 'OK.La imagen se ha subido correctamente',
                        'image_name' => $image_path_unique,
                        "image_url" => Storage::disk('public')->url($uploaded_image),
                        'mime' => $image->getClientMimeType(),
                    ];
                }else{
                    $data = [
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'No se pudo almacenar la imagen el el servidor'
                    ];
                }


            }else{
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => ' ERROR. Error al subir la imagen'
                ];
            }

        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => ' Request does not contain a file'
            ];
        }

        return response()->json($data, $data['code']);
    }


    /**
     * Get user avatar.
     *
     * @param $filename
     * @return JsonResponse|Response
     * @throws FileNotFoundException
     */
    public function getImage($filename)
    {
        $isset_file = Storage::disk('users')->exists($filename);

        if($isset_file){
            $file = Storage::disk('users')->get($filename);
            return new Response($file, 200);

            /*
            // Return json.
            $data = array(
                'code' => 200,
                'status' => 'success',
                'image' => base64_encode($file)
            );
            return response()->json($data, $data['code']);
            */
        }
        else{
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'ERROR.La imagen no existe'
            ];
            return response()->json($data, $data['code']);
        }

    }


    /**
     * Get data profile user
     *
     * @param  $id
     * @return JsonResponse
     */
    public function profile($id): JsonResponse
    {
        $user = User::find($id);

        if(is_object($user)){
            $data = [
                'code' => 200,
                'status' => 'success',
                'user' => $user
            ];
        }else{
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El usuario no existe',
            ];
        }
        return response()->json($data, $data['code']);
    }

}
