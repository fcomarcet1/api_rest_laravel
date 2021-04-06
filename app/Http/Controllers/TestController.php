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
class TestController extends Controller
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


}
