<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
class AuthController extends Controller
{
    public function login_page(){
        return view('login');
    }

    public function signup_page(){
        return view('signup');
    }


    public function login(Request $request){
        
        if(Auth::attempt($request->only('email','password'))){
            $request->session()->regenerate();


            return redirect()->to('/')->with('success','loginned');
        }

        return back()->with('error','can not login');
    }


    public function signup(Request $request){
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->to('/')->with('success','logined');
    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'logged out');
    }
}
