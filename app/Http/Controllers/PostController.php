<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * @param Request $request
     * @return string
     */
    public function test(Request $request): string
    {
        return "Metodo de pruebas PostController";
    }
}
