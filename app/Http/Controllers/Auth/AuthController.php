<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
use App\Models\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Permission;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Carbon\Carbon;

class AuthController extends Controller
{    	
    public function redirect(string $driver = 'google')
    {
        return Socialite::driver($driver)->redirect();
    }
    
    public function callback(string $driver = 'google')
    {
        $user = Socialite::driver($driver)->user();

        $dbUser = User::where('email', $user->email)->first();
    
        if($dbUser)
        {
            Auth::login($dbUser);
        }
        else
        {
            $codigo = User::gerarCodigoPessoaExterna();

            $dbUser = User::create([
                'name'   => $user->getName(),
                'email'  => $user->getEmail(),
                'email_verified_at' => date('Y-m-d H:i:s'),
                'codpes' => $codigo,
                'driver' => $driver,
            ]);

            $dbUser->markEmailAsVerified();

            /* Monta as permissões do usuário */
            $permissions[] = Permission::where('guard_name', 'senhaunica')->where('name', 'user')->first();
            $permissions[] = Permission::where('guard_name', 'senhaunica')->where('name', 'Outros')->first();
            
            $dbUser->syncPermissions($permissions);
            
            Auth::login($dbUser);

            Utils::setSession($dbUser->id);
        }

        return redirect('/');
    }
}
