<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

/**
 * Class CategoryController
 * @package App\Http\Controllers
 */
class CategoryController extends Controller
{
    /**
     * CategoryController constructor.
     */
    public function __construct()
    {
        $this->middleware('api.auth')->except(['index', 'show',]);
        //$this->middleware('api.auth')->only(['store', 'edit',]);
    }


    /**
     * Display a listing of the resource (Categories).
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $categories = Category::all();

        if ($categories->isNotEmpty() && is_object($categories)){
            $data = [
                'code' => 200,
                'status' => 'success',
                'categories' => $categories,
            ];
        }
        else{
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'No existen categorias actualmente',
            ];
        }
        return response()->json($data, $data['code']);
    }


    /**
     * Display the specified resource (Category).
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $category = Category::find($id);

        if (is_object($category)){
            $data = [
                'code' => 200,
                'status' => 'success',
                'categories' => $category,
            ];
        }
        else{
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'No existe la categoria seleccionada',
            ];
        }
        return response()->json($data, $data['code']);
    }


    /**
     * Store a newly created resource in storage (new category).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {

        // Get data from request
        $json = $request->input('json', null);


        // Decode json && Get data in associative array
        $params_array = json_decode($json, true);


        if (!empty($json) && !empty($params_array) ){
            dump($json);
            dump($params_array); die();

            // validate data
            $validate = Validator::make($request->all(), [
                'name' => 'required|string'
            ]);

            // Check if validate fails


            // Save in DB
            // Category::create($request->only("name"));

            // Return json data
            $data = [
                'code' => 200,
                'status' => 'succes',
                'message' => 'OK. Categoria añadida correctamente '
            ];
        }
        else { // Empty data from request return error
            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => 'ERROR. Los datos enviados no se recibieron',
            ];
        }

        return response()->json($data, $data['code']);
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
