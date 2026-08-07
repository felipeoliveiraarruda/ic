<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Arr;
use App\Models\User;
use App\Models\Utils;
use App\Models\Projetos;

class HomeController extends Controller
{
    public function index()
    {
        if (Auth::guest())
        {            
            return view('index', [
                'projetos' => Projetos::all()
            ]);
        }
        else
        {            
            Utils::setSession(Auth::user()->id);
            
            if (session('level') == 'admin' || Arr::exists(session('vinculos'), 'Docente'))
            {
                return redirect(route('admin.dashboard'));
            }
            else if (session('level') == 'user')
            {
                if (session()->has('url.intended'))
                {
                    return redirect()->intended();
                }
                else
                {
                    return redirect(route('admin.projetos'));
                }
            }
            else
            {
                return redirect('dashboard');
            }
        }
    }

    public function show($codigoProjeto)
    {
        return view('visualizar',
        [
            'projeto' => Projetos::find($codigoProjeto),
        ]);
    }
}