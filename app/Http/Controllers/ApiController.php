<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\ForgetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{   
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50',
            'phone' => 'required|min:10'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->phone = $request->phone;
        $user->save();
        //User created, return success response
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 200);
    }
 
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                	'success' => false,
                	'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
    	return $credentials;
            return response()->json([
                	'success' => false,
                	'message' => 'Could not create token.',
                ], 500);
        }
 	
 		//Token created, return with success response and jwt token
        return response()->json([
            'success' => true,
            'token' => $token,
        ]);
    }
 
    public function logout(Request $request)
    {
        $token = $request->header('Authorization');
      

		//Request is validated, do logout        
        try {
            JWTAuth::invalidate($token);
 
            return response()->json([
                'success' => true,
                'message' => 'User has been logged out'
            ],200);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
 
    public function get_user(Request $request)
    {
        $token = $request->header('Authorization');
        $user = JWTAuth::authenticate($token);
        if($user)
        {
            return response()->json(['user' => $user], 200);
        }
        else
        {
            return response()->json(['error'=>'something went wrong try again']);
        }
    }

    public function update_user(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|min:10'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }

        $token = $request->header('Authorization');
        $user = JWTAuth::authenticate($token);
        $data = User::find($user->id);
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->save();
        return response()->json(['success'=>true, 'data'=>$data], 200);

    }

    public function change_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'newpassword' => 'required|string|min:6|max:50',
            'cpassword' => 'required|string|min:6|max:50|same:newpassword'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        } 
        else 
        {
            $token = $request->header('Authorization');
            $user = JWTAuth::authenticate($token);
            $data = User::find($user->id);
            $data->password = Hash::make($request->newpassword);
            $data->save();

            JWTAuth::invalidate($token);
            return response()->json([
                'success' => true,
                'message' => 'Password has been changed successfully login agian to contiune'
            ],200);
        }
    }

    public function forget_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        } 
        $emailExits = User::where('email', $request->email)->count();
        if($emailExits > 0)
        {
            $token = random_int(1000000, 9999999);
            $email = $request->email;
                DB::table('password_resets')->insert(
                    ['email' => $email, 
                    'token' => $token, 
                    'created_at' => Carbon::now()]
                );
            Mail::to($request->email)->send(new ForgetPassword($token));
        
            return response()->json(['success' => true,'message'=>'Email Sended Sucessfully'], 200);
        }
        return response()->json(['success' =>false, 'message'=>'Email does not exist in database'], 200);

        
    }

    public function resetPasswordPage($token)
    {
        $tokenExits = DB::table('password_resets')->where('token', $token)->count();
        if($tokenExits > 0)
        {
            return view('resetPassword', compact('token'));   
        }
        else
        {
            return 'Link Expired Please Try again!';
        }
    }

    public function submitPassword(Request $request)
    {
        $token = $request->token;
        $data = DB::table('password_resets')->where('token', $token)->get();
        
        if($user = DB::table('users')->where('email', $data[0]->email)->update([
            'password' => Hash::make($request->password)
        ]))
        {
            DB::table('password_resets')->where('token', $token)->delete();
            return response()->json(['success'=>true, 'message'=>'Password reseted success Please login']);
        }

        
        DB::table('password_resets')->where('token', $token)->delete();
        return response()->json(['success'=>false, 'message'=>'Password reset error Please again']);
        
               
    }
}
