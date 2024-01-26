<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class FromController extends Controller
{

    public function fromLogin(Request $request):  Response
    {
        return response()->view("from");
    }

    public function fromLoginSubmit(LoginRequest $request): Response
    {
        // $rule = [
        //     "username" => ["required"],
        //     "password" => ["required"]
        // ];

        // $data = $request->validate();
        $data = $request->validated();

        Log::info(json_encode($request->all(), JSON_PRETTY_PRINT));


        return response("oke", Response::HTTP_OK);
    }




    public function login(Request $request): Response
    {
        try {

            $rule = [
                "username" => ["required"],
                "password" => ["required"]
            ];

            $data = $request->validate($rule);

            // kita bisa melakukan manipulasi data disini seteleh validate contohnya terhubung dengan database

            return response("OK", Response::HTTP_OK);

        }catch (ValidationException $validationException) {
            return response($validationException->errors(), Response::HTTP_BAD_REQUEST);
        }
    }
}
