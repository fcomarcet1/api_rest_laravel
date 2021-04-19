<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\Models\Post;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * PostController constructor.
     */
    public function __construct()
    {
        $this->middleware('api.auth')->except('index', 'show');
    }


    /**
     * Display a listing of the resource (Posts List).
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // Get all posts
        // $posts = Post::all();
        // $posts = Post::paginate(10)->load('category');
        $posts = Post::latest()->paginate(10)->load('category');

        if (is_object($posts) && $posts->count() >= 1 ){

            $data = [
                'code' => 200,
                'status' => 'success',
                'posts' => $posts
            ];
        }
        else{
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'No hay ningun post actualemente.'
            ];
        }
        return response()->json($data, $data['code']);
    }


    /**
     * Display the specified resource (Post detail).
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $post = Post::find($id);

        if (is_object($post)){

            //Get category of this post.
            $post = $post->load('category');

            $data = [
                'code' => 200,
                'status' => 'success',
                'post'=> $post
            ];
        }
        else{
            $data = [
                'code' => 404,
                'status' => 'error',
                'message'=> 'El post no existe',
            ];
        }

        return response()->json($data, $data['code']);
    }


    /**
     * Store a newly created resource in storage (Save new post).
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(Request $request): JsonResponse
    {
        // Get data from request.
        $json = $request->input('json', null);
        $params = json_decode($json); // obj
        $params_array = json_decode($json, true); // associative array

        if (!empty($json) && !empty($params_array) ){

            // Get data from user logged
            $token = $request->header('Authorization');
            $JwtAuth = new JwtAuth();
            $user = $JwtAuth->checkToken($token, true);

            // validate data from request
            $validate = Validator::make($params_array, [
                'title'=> 'required|string|unique:posts|min:2|max:255',
                'content'=> 'required',
                'image' => 'required',
                'category_id' => 'required|numeric',
            ]);

            // Check validate
            if ($validate->fails()){
                $data = [
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'ERROR al crear el post rellena los campos correctamente.',
                    'error' => $validate->errors(),
                ];
            }else{
                // Add user_id to params_array
                $params_array['user_id'] = $user->sub;
                // dump($params_array);

                // Ste data in new obj
                $post = new Post();
                $post->user_id = $user->sub;
                $post->category_id = $params->category_id;
                $post->title = $params->title;
                $post->content = $params->content;
                $post->image = $params->image;
                $post->updated_at = null;

                // Save data in DB.
                $post_saved = $post->save();

                if ($post_saved){
                    $data = [
                        'code' => 201,
                        'status' => 'success',
                        'message' => 'OK. Post añadido correctamente.',
                        'post' => $post,
                    ];
                }
                else{
                    $data = [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'ERROR. Error al guardar el registro en BD',
                    ];
                }
            }
        }
        else{
            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => 'ERROR. Los datos enviados no se recibieron',
            ];
        }

        //Return response
        return response()->json($data, $data['code']);
    }


    /**
     * Update the specified resource in storage (Update post).
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function update(int $id, Request $request): JsonResponse
    {
        // Get data from request
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        $data = [];

        if (empty($json) || empty($params_array) || empty($params)){

            $data['code'] = 400;
            $data['status'] = 'error';
            $data['message'] = 'ERROR. Los datos no se recibieron correctamente.';

            return response()->json($data, $data['code']);
        }

        // Get user logged
        $jwtAuth = new JwtAuth();
        $userAuth = $jwtAuth->getIdentity($request);

        // Validate data from request
        $validate = Validator::make($params_array, [

            'title' => 'required|unique:posts,title,' . $userAuth->sub,
            'content' => 'required',
            //'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:20048',
            'category_id' => 'required',
        ]);

        if ($validate->fails()){

            $data['code'] = 400;
            $data['status'] = 'error';
            $data['errors'] = $validate->errors();

            return response()->json($data, $data['code']);
        }

        // Check if exists at registry
        $post_find = Post::find($id);

        if (empty($post_find)){

            $data['code'] = 400;
            $data['status'] = 'error';
            $data['message'] = 'ERROR. El post que deseas eliminar no existe.';

            return response()->json($data, $data['code']);
        }


        // Find registry for update and check (user logged = user_id)->owner.
        $post = Post::where('id',$id)->where('user_id', $userAuth->sub)->first();

        if (empty($post)){

            $data['code'] = 401; //401 Unauthorized
            $data['status'] = 'error';
            $data['message'] = 'ERROR. No puedes modificar ese post.';

            return response()->json($data, $data['code']);
        }

        // Eliminar lo que no queremos actualizar
        unset($params_array['id']);
        unset($params_array['user_id']);
        unset($params_array['created_at']);
        unset($params_array['user']);

        // Conditions for update
        $where = [
            'id'=> $id,
            'user_id' => $userAuth->sub
        ];

        // Actualizar el registro en concreto
        //dump($params_array); die();
        $post_update = Post::updateOrCreate($where, $params_array);

        if (empty($post_update)){

            $data['code'] = 400;
            $data['status'] = 'error';
            $data['message'] = 'ERROR. No se pudo actualizar el post.';

            return response()->json($data, $data['code']);
        }

        // Return success response
        $data['code'] = 200;
        $data['status'] = 'success';
        $data['post'] = $post_update;
        //$data['changes'] = $params_array;

        return response()->json($data, $data['code']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(int $id, Request $request): JsonResponse
    {
        $data = [];

        // Get user logged
        $jwtAuth = new JwtAuth();
        $userAuth = $jwtAuth->getIdentity($request);

        // Check if exists at registry
        $post_find = Post::find($id);

        if (empty($post_find)){

            $data['code'] = 400;
            $data['status'] = 'error';
            $data['message'] = 'ERROR. El post que deseas eliminar no existe.';

            return response()->json($data, $data['code']);
        }

        // Find registry for delete and check (user logged = user_id)->owner.
        $post = Post::where('id',$id)->where('user_id', $userAuth->sub)->first();
        // $post = Post::find($id);

        if (empty($post)){
            $data['code'] = 400;
            $data['status'] = 'error';
            $data['message'] = 'ERROR.No eres el propietario del post.';

            return response()->json($data, $data['code']);
        }

        // Delete post
        $delete = $post->delete();

        if (empty($delete)){

            $data['code'] = 400;
            $data['status'] = 'error';
            $data['message'] = 'ERROR.No se pudo eliminar el registro.';

            return response()->json($data, $data['code']);
        }

        // Return success response
        $data['code'] = 200;
        $data['status'] = 'success';
        $data['message'] = 'El registro se elimino correctamente';

        return response()->json($data, $data['code']);

    }



    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        // Return view
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }




}
