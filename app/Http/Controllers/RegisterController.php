<?php

namespace App\Http\Controllers;


use App\User;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function store(Request $request){


      $messages = [
          'required'=> 'El campo :attribute es requerido',
          'string'=> 'Debe ingresar un dato de texto en el campo :attribute',
          'confirmed'=> 'Ambas contraseñas deben coincidir',
          'unique' => 'email_exists',
          'min'=> 'La contraseña debe tener al menos 6 caracteres',
          "email" => "Debe ingresar una cuenta de email válida"
      ];

      $validator = Validator::make($request->all(), [
          'name' => ['required', 'string', 'max:255'],
          'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
      ],$messages);


      if($validator->fails()){
        $errors = (array) $validator->errors()->messages();
        $user = User::where( "email", $request["email"])->get()->first();
        if($errors["email"][0] == "email_exists"){
          return response()->json([
            "status" => "fail",
            "data" => $validator->errors(),
            "token" => $user->api_token
          ]);
        }else{
          return response()->json([
            "status" => "fail",
            "data" => $validator->errors()
          ]);
        }

      }

      $token = Str::random(30);
      $user = User::create([
          'name' => $request['name'],
          'email' => $request['email'],
          'colegio' => $request['colegio'],
          'api_token' => $token,
          'password' => Hash::make("MOONTRAVEL"),
          "tipo"=> $request["tipo"],
          "photo_url"=> $request["photo_url"]

      ]);

      return response()->json([
        "status" => "success",
        "data" => $token
      ]);
    }

}
