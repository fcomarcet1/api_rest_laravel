<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class TestController
 * @package App\Http\Controllers
 */
class TestController extends BaseController
{
   public function test(Request $request): string
   {
       return "Prueba del TestController";
   }

   /**
     * @param Request $request
     * @return JsonResponse
     */
   public function testJson(Request $request): JsonResponse
    {
        $data = [
            'status' => 'error',
            'code' => 404,
            'message' => 'Error. El usuario no se ha creado'

        ];

        return response()->json($data, $data['code']);
    }

   public function testORM()
   {
       $users = User::all();
       //dump($users);
       foreach ($users as $user){
           //dump($user->name);
           //dump($user->email);
       }
       $email = 'fcomarcet1@gmail.com';
       $password = '$2y$10$RFOGHzpFX3FprG1UqtJoV.yKT3vHlexf4O1SmVouBfFjHcJfRHVPC';

       $user = User::where([
           'email' => $email,
           'password' => $password
       ])->first();

       dump($user);
       dump(is_object($user));
       /*
       $posts = Post::all();
       //dump($posts);
       foreach($posts as $post){
           echo "Title";
           dump($post->title);
           echo "Owner";
           dump($post->user->name);
           echo "category";
           dump($post->category->name);
       }
       */
       die();
   }


   public function index(): JsonResponse
   {
       $result = Post::all();
       return $this->sendResponse($result,200, 'Posts retrieved successfully.');
   }

    public function store(Request $request): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'vat_number' => 'required|digits:10',
            'street' => 'required',
            'city' => 'required',
            'post_code' => 'required|regex:/^([0-9]{2})(-[0-9]{3})?$/i',
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), 'Validation Error.', 400);
        }

        $post = Post::create($input);

        return $this->sendResponse($post->toArray(), 'Client created successfully.');
    }





}
