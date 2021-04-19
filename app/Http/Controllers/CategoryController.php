<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\Models\Category;
use Exception;
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
        $this->middleware('api.auth.admin')->only(['store', 'update']);
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

            // validate data
            $validate = Validator::make($params_array, [
                'name' => 'required|string|unique:categories'
            ]);

            // Check if validate fails
            if ($validate->fails()){
                $data = [
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'ERROR. La categoria no se ha creado. ya existe una categoria con ese nombre',
                    'errors' => $validate->errors()
                ];
            }
            else{
                // Save in DB
                    // $category = new Category();
                    // $category->name = $params_array['name'];
                    // $save = $category->save();

                $category = Category::create($params_array);

                if (is_object($category)){
                    $data = [
                        'code' => 200,
                        'status' => 'success',
                        'message' => 'OK. Categoria añadida correctamente.',
                        'category' => $category,
                    ];

                }else{
                    $data = [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'ERROR. Error al guardar el registro en BD',
                    ];
                }

            }

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
        //  return view('category.create');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        // $category = Category::find($id);
        // return view('categories.edit', compact('category'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Category $category
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function update(Category $category, Request $request): JsonResponse
    {
        // Si recibe el objeto category directamente obtenemos la categoria a modificar
        // ya que al pasarle el id como parametro en la url obtiene el objeto de dicho id
        // dump($category->id, $category->name);die();

        // Get data from request
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        //dump($user_role);die();

        if (!empty($json) && !empty($params_array) ){

            // Get data from user logged
            $token = $request->header('Authorization');
            $JwtAuth = new JwtAuth();
            $user = $JwtAuth->checkToken($token, true);
            $user_role = $user->role;

            // Validate data
            $validate = Validator::make($params_array, [
                'name' => 'required|string|unique:categories,name,' . $category->id
            ]);

            if ($validate->fails()) {
                $data = [
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'ERROR.Introduce una categoria valida.',
                    'error' => $validate->errors(),
                ];
            }
            else {
                $params_array['updated_at'] = date("Y-m-d H:m:s");

                // Quitar campos que no queremos actualizar
                // unset($params_array['id']);
                unset($params_array['created_at']);

                $updatedCategory = $category->fill($params_array)->save();

                if($updatedCategory){
                    $data = [
                        'code' => 200,
                        'status' => 'success',
                        'message' => 'OK. Categoria actualizada correctamente.',
                        'category' => $category,
                    ];
                }
                else{
                    $data = [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'ERROR.No se pudo actualizar la categoria.',
                    ];
                }

            }
        }
        else{ // Empty data from request return error
            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => 'ERROR. Los datos enviados no se recibieron',
            ];
        }
        return response()->json($data, $data['code']);
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
