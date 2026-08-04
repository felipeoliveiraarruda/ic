<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class Utils extends Model
{
    use HasFactory;

    public static function setSession($id)
    {
        $level = User::obterLevel($id);        
        session(['level' => '']);
        session(['level' => $level]);

        $vinculos = User::obterVinculos($id);
        session(['vinculos' => '']);
        session(['vinculos' => $vinculos]);
    }

    function tratarNome($nome)
    {
        $saida = '';
        $nome = strtolower($nome); // Converter o nome todo para minúsculo
        $nome = explode(" ", $nome); // Separa o nome por espaços
        
        for ($i=0; $i < count($nome); $i++) 
        {
    
            // Tratar cada palavra do nome
            if ($nome[$i] == "de" or $nome[$i] == "da" or $nome[$i] == "e" or $nome[$i] == "dos" or $nome[$i] == "do") {
                $saida .= $nome[$i].' '; // Se a palavra estiver dentro das complementares mostrar toda em minúsculo
            }else {
                $saida .= ucfirst($nome[$i]).' '; // Se for um nome, mostrar a primeira letra maiúscula
            }
    
        }

        return $saida;
    }
}