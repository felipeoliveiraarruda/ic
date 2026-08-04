<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use \Spatie\Permission\Traits\HasRoles;
    use \Uspdev\SenhaunicaSocialite\Traits\HasSenhaunica;

    protected $guard_name = 'senhaunica';
    protected $dates = ['email_verified_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf',
        'codpes',
        'driver',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function gerarCodigoPessoaExterna()
    {
        $tamanho = 5;
        $prefixo = "88";

        $temp = User::count() + 1;

        $codigo = str_pad($temp, $tamanho, '0', STR_PAD_LEFT);

        return $prefixo.$codigo;
    }

    public function setCodpesAttribute($value) 
    {
        $this->attributes['codpes'] = preg_replace('/[^0-9]/', '', $value);
    }

        public static function obterLevel($id)
    {
        $user = User::with('permissions', 'roles')->find($id);
        return $user->level;
    }

    public static function obterVinculos($id)
    {
        $user = User::with('permissions', 'roles')->find($id);   
        $vinculos = array();
        
        foreach ($user->permissions->where('guard_name', User::$vinculoNs)->whereIn('name', User::$permissoesVinculo) as $p)
        {
            array_push($vinculos, $p->name);
        }

        return $vinculos;
    }
}
