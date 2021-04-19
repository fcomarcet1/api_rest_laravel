<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @param $result
     * @param int $code
     * @param null $message
     * @return JsonResponse
     */
    public function sendResponse($result, int $code, $message = null): JsonResponse
    {
        $response = [
            'code' => $code,
            'success' => true,
            'data' => $result,
        ];

        if (isset($message)) {
            $response['message'] = $message;
        }

        return response()->json($response, $response['code']);
    }


    /**
     * return error response.
     *
     * @param $message
     * @param array $errors
     * @param int $code
     * @return JsonResponse
     */
    public function sendError(Array $errors, $message = null, $code = 404): JsonResponse
    {
        $response = [
            'code' => $code,
            'success' => false,
        ];

        if (isset($message)) {
            $response['message'] = $message;
        }

        if (!empty($errors)) {
            $response['error'] = $errors;
        }

        return response()->json($response, $code['code']);
    }
}
