<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Projetos extends Model
{
    use \Spatie\Permission\Traits\HasRoles;
    use HasFactory, Notifiable, SoftDeletes;

    protected $primaryKey = 'codigoProjeto';
    protected $table      = 'projetos';
        
    protected $fillable = [
        'tituloProjeto',
        'descricaoProjeto',
        'numeroHorasProjeto',
        'periodoProjeto',
        'informacoesProjeto'
    ]; 
}
