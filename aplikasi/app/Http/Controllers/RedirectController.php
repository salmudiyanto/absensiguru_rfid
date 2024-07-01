<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function cek(){
        if(auth()->user()->roles_id ===1){
            return redirect('/administrator');
        }else if(auth()->user()->roles_id ===2){
            return redirect('/kepsek');
        }else if(auth()->user()->roles_id ===3){
            return redirect('/guru');
        }else{
            return redirect('/logout');
        }
    }
}
