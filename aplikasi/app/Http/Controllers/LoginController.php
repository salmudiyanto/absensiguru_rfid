<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function halamanlogin(){
        return view('page.login');
    }

    public function proseslogin(Request $req){
        $credential = $req->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        if(auth()->attempt($credential)){
            $req->session()->regenerate();
            if(auth()->user()->roles_id === 1){
                return redirect()->intended('/administrator');
            }else if(auth()->user()->roles_id === 2){
                return redirect()->intended('/kepsek');
            }else if(auth()->user()->roles_id === 3){
                return redirect()->intended('/guru');
            }else{
                auth()->logout();
                $req->session()->invalidate();
                $req->session()->regenerateToken();
                return redirect('/');
            }
        }else{
            return back()->with('error', 'Email atau Password tidak ditemukan');
        }
    }

    public function logout(Request $req){
        auth()->logout();
        $req->session()->invalidate();
        $req->session()->regenerateToken();
        return redirect('/');
    }

}
