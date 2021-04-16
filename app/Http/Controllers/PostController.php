<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
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
