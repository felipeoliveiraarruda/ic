<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Traits\Auditavel;
use USPdev\Replicado\Pessoa;

class Projeto extends Model
{
    use \Spatie\Permission\Traits\HasRoles;
    use HasFactory, Notifiable, SoftDeletes, Auditavel;
       
    protected $fillable = [
        'codigoPessoaResponsavel',
        'tituloProjeto',
        'descricaoProjeto',
        'linhaPesquisaProjeto',
        'statusExternoProjeto',
        'tipoBolsaProjeto',
        'bolsaProjeto',
        'numeroVagaProjeto',
        'dataInicioProjeto',
        'dataTerminoProjeto',
        'informacoesProjeto',
        'codigoPessoaCriacao',
        'codigoPessoaAlteracao',
    ];

    protected $casts = [
        'dataInicioProjeto' => 'date',
        'dataTerminoProjeto' => 'date',
    ];

    public function preRequisitos(): HasMany
    {
        return $this->hasMany(PreRequisito::class, 'codigoProjeto');
    }

    public function editais(): HasMany
    {
        return $this->hasMany(Edital::class, 'codigoProjeto');
    }

    // Exemplo no Model Projeto.php
    public function getResponsavelAttribute(): ?array
    {
        return $this->codigoPessoaResponsavel 
            ? Pessoa::dump($this->codigoPessoaResponsavel) 
            : null;
    }

    public function getCriadorAttribute(): ?array
    {
        return $this->codigoPessoaCriacao 
            ? Pessoa::dump($this->codigoPessoaCriacao) 
            : null;
    }    
}