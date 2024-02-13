<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api',['except'=>['login','register','forgotPassword','resetPassword']]);
    }

    public function register(Request $request){
        $validated=Validator::make($request->all(),
            [
                'name'=>"required|min:2|max:255",
                'email'=>['required','unique:users','max:255','email'],
                'password'=>'required|min:8|max:255'
            ]
        );
        if(!$validated->fails()){
            $user=User::create([
                'name'=> $request->name,
                'email'=>$request->email,
                'password'=>Hash::make($request->password)
            ]);
            return response()->json(['message'=>'User registered successfully','user'=>$user],200);
        }
        else {
            return response()->json(['message' => $validated->errors()->first()], 422);
        }
    }


    public function login(Request $request){
        $validated=Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required|string'
        ]);
        if(!$validated->fails()) {
            $credentials = $request->only(
                'email',
                'password'
            );
            $token = Auth::attempt($credentials);
            if (!$token) {

                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }
            $user = Auth::user();
            return response()->json([
                'user' => $user,
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer'
                ]
            ]);
        }
        else{
            return response(['message'=>$validated->errors()->first()],422);
        }
    }

    public function logout(){
        Auth::logout();
        return response()->json([
           'message'=>'Successfully logged out'
        ]);

    }


    public function forgotPassword(Request $request){
        $validated=Validator::make($request,[
           'email'=> "required|email|"
        ]);
    }

    public function resetPassword(){

    }


    //
}
