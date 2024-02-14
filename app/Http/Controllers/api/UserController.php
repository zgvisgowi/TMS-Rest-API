<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;


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
        $validated=Validator::make($request->all(),[
           'email'=> "required|email|exists:users,email"
        ]);
        if($validated->fails()){
            return response()->json([
                'meassage'=>$validated->errors()->first()
            ]);
        }
            $status=Password::sendResetLink(
              $request->only('email')
            );

            return $status===Password::RESET_LINK_SENT
                ? response()->json(['success'=>true,'status'=>__($status)])
                : response()->json(['success'=>false,'status'=>__($status)]);
    }

    public function resetPassword(Request $request){
        $request->validate([
           'token'=>'required',
           'email'=>'required|email',
           'password'=>'required|min:8'
        ]);
        $status=Password::reset(
            $request->only('email','password','token'),
            function (User $user,string $password){
                $user->forceFill(['password'=>Hash::make($password)])->setRememberToken(Str::random(9));
                $user->save();
                event(new PasswordReset($user));
            }
        );
        if($status===Password::PASSWORD_RESET){
            return response()->json(['success'=>true,'status'=>__($status)]);
        }
        return response()->json(['success'=>false,'status'=>__($status)]);

    }


    //
}
