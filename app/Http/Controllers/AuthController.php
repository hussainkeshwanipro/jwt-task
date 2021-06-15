<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Twilio\Rest\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    //login page 
    public function login()
    {
        return view('pages.login');
    }
   
    //post login with validation 
    public function postLogin(Request $request)
    {
        $request->validate([
            'phone' => 'required|exists:users|min:10',
            'email' => 'required|email|exists:users',
            'password' => 'required|min:5'
        ]);

        $account_sid = 'ACcc4d44fba5d5d4fb2dab5a45a19f3aff';
        $auth_token = 'd258df3db581d34343e5149f730b2088';
        $twilio_number = "+13059648388";
        
        $myno = '+91'.$request->phone;//phone number 
        $otp = rand(1111, 9999);//opt
        $client = new Client($account_sid, $auth_token);

        //sending otp
        $client->messages->create(
            // Where to send a text message (your cell phone?)
            $myno,
            array(
                'from' => $twilio_number,   
                'body' => $otp
            )
            
        );

        //stored otp to verified phone in next step
        DB::table('sms')->insert([
            'phone' => $request->phone,
            'otp' => $otp, 
            'created_at' => Carbon::now()
        ]);
           
       //user data saved in session to login it in next step
        Session::put('data',$request->all());
        return redirect()->route('otpPage')->with('success', 'otp sended successfully');
    }


    //register page
    public function register()
    {
        return view('pages.register');
    }

    //post register with validation
    public function postRegister(Request $request)
    {
            $request->validate([
                'fname' => 'required|min::2',
                'lname' => 'required|min:2',
                'email' => 'required|min:5|email|unique:users',
                'password' => 'required|min:5',
                'cpassword' => 'required|min:5|same:password',
                'phone' => 'required|min:5|min:10|unique:users',
            ]);


            //twilio details
            $account_sid = 'ACcc4d44fba5d5d4fb2dab5a45a19f3aff';
            $auth_token = 'd258df3db581d34343e5149f730b2088';
            $twilio_number = "+13059648388";
            
            $myno = '+91'.$request->phone;//phone number 
            $otp = rand(1111, 9999);//opt
            $client = new Client($account_sid, $auth_token);

            //sending otp
            $client->messages->create(
                // Where to send a text message (your cell phone?)
                $myno,
                array(
                    'from' => $twilio_number,
                    'body' => $otp
                )
            );

            //stored otp to verified phone in next step
            DB::table('sms')->insert([
                'phone' => $request->phone,
                'otp' => $otp, 
                'created_at' => Carbon::now()
            ]);
            
        //user data saved in session to register it in next step
            Session::put('data',$request->all());
        return redirect()->route('otpPage')->with('success', 'otp sended successfully');
    }

    //otp page
    public function otpPage()
    {
       if(Session::has('data'))
       {
           return view('pages.otpPage');
       }
       else
       {
            return redirect()->route('login')->with('error', 'Session Expired!');

       }
    }

    //post opt with validation
    public function postOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|min:4',
        ]);

        //after validation passed
        $data = Session::get('data');
        
        //condition to check user exits or not
        $count = User::where('phone', $data['phone'])->count();
        if($count > 0)
        {
        //login conditon 
            $otp = DB::table('sms')->where('phone', $data['phone'])->get();//get opt data stored in sms table earlier
            
            //check otp condition
            if($otp[0]->otp == $request->otp)
            {
               $email = $data['email'];
               $password = $data['password'];
                if(Auth::attempt(['email' => $email, 'password' => $password]))
                {
                    $delete_otp = DB::table('sms')->where('phone', $data['phone'])->delete();
                    // return $request->otp; 
                
                    return redirect()->route('userDashboard')->with('success', 'Welcome '. Auth::user()->name);
                    

                }
                else
                {
                    $delete_otp = DB::table('sms')->where('phone', $data['phone'])->delete();
                    return redirect()->route('otp')->with('error', 'Login failed Resend Otp');
                }
                
            }
            else
            {
                //above conditon fail than redirect to login page session destory, otp deleted and cycle start again
                $delete_otp = DB::table('sms')->where('phone', $data['phone'])->delete();
                Session::flush();

                return redirect()->route('login')->with('error','Login Error Try again');
            }
        }
        else
        {
        //register conditon 
            $otp = DB::table('sms')->where('phone', $data['phone'])->get();//get opt data stored in sms table earlier
        
            //check otp condition
            if($otp[0]->otp == $request->otp)
            {
                $user = new User();
                $user->fname = $data['fname'];
                $user->lname = $data['lname'];
                $user->email = $data['email'];
                $user->password = Hash::make($data['password']);
                $user->phone = $data['phone'];
                $user->phone_verified = true;
                $user->save();
                
                $delete_otp = DB::table('sms')->where('phone', $data['phone'])->delete();
                Session::flush();

                return redirect()->route('login')->with('success','Mobile Number Verified Successfully');
                
            }
            else
            {
                //above login or register doesnot work than otp delete 
                $delete_otp = DB::table('sms')->where('phone', $data['phone'])->delete();
                return redirect()->back('error', 'Error Occur resend opt');
            }
        }
        
        
    }

    //resendOtp
    public function resendOtp()
    {
        if(Session::has('data'))
        {
            $data = Session::get('data');
            //Delete Previous otp
            $delete_otp = DB::table('sms')->where('phone', $data['phone'])->delete();
            
            //resend otp
            $account_sid = 'ACcc4d44fba5d5d4fb2dab5a45a19f3aff';
            $auth_token = 'd258df3db581d34343e5149f730b2088';
            $twilio_number = "+13059648388";
            
            $myno = '+91'.$data['phone'];//phone number 
            $otp = rand(1111, 9999);//opt
            $client = new Client($account_sid, $auth_token);
            $client->messages->create(
                // Where to send a text message (your cell phone?)
                $myno,
                array(
                    'from' => $twilio_number,
                    'body' => $otp
                )
            );

            //stored new otp to verified next
            DB::table('sms')->insert([
                'phone' => $data['phone'],
                'otp' => $otp, 
                'created_at' => Carbon::now()
            ]);

            return redirect()->route('otpPage')->with('success', 'opt resended!');
        }
        else
        {
            return redirect()->route('login')->with('error', 'Session Expired!');
        }
    }

    public function session_flush()
    {
        Session::flush();
        return redirect()->route('login')->with('success', 'all Session flush');
    }

    public function logout()
    {
        Auth::logout();
        Session::flush();
        return redirect()->route('login')->with('success', 'Logout Success');
    }


    public function userDashboard()
    {
        if(Auth::check())
        {
            $user = Auth::user();
            return view('user.home', compact('user'));
        }
        else
        {
            return redirect()->route('login')->with('error', 'Login to contiune');
        }
    }
}
