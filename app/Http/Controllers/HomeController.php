<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use App\Models\User;
use App\Models\Utils;

class HomeController extends Controller
{
    public function index()
    {
        if (Auth::guest())
        {
            return view('index');
        }
        else
        {            
            Utils::setSession(Auth::user()->id);            
            
            if (session('level') == 'admin')
            {
                return redirect('admin');
            }
            else if (Arr::exists(session('vinculos'), 'Docente'))
            {
                return redirect('admin');
            }
            else
            {
                return redirect('dashboard');
            }
        }
    }
}