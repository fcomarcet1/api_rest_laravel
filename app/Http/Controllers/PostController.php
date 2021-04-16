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
        //$posts = Post::all();
        $posts = Post::paginate(10)->load('category');

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
                        'code' => 200,
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

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
