<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Projeto extends Model
{
    use \Spatie\Permission\Traits\HasRoles;
    use HasFactory, Notifiable, SoftDeletes;
       
    protected $fillable = [
        'codigoPessoa',
        'codigoCurso',
        'tituloProjeto',
        'periodoProjeto',
        'linhaPesquisaProjeto',
        'statusExternoProjeto',
        'tipoBolsaProjeto',
        'bolsaProjeto',
        'dataInicioProjeto',
        'dataTerminoProjeto',
        'descricaoProjeto',
        'informacoesProjeto',
        'codigoPessoaCriacao',
        'codigoPessoaAlteracao',
    ];

    protected $casts = [
        'dataInicioProjeto' => 'date',
        'dataTerminoProjeto' => 'date',
    ];
}