<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Traits\Auditavel;

class Edital extends Model
{
    use \Spatie\Permission\Traits\HasRoles;
    use HasFactory, Notifiable, SoftDeletes, Auditavel;

    protected $fillable = [
        'codigoProjeto',
        'dataInicioEdital',
        'dataTerminoEdital',
        'codigoPessoaCriacao',
        'codigoPessoaAlteracao',
    ];

    protected $casts = [
        'dataInicioEdital' => 'date',
        'dataTerminoEdital' => 'date',
    ];

    public function projeto(): BelongsTo
    {
        return $this->belongsTo(Projeto::class, 'codigoProjeto');
    }

    public function inscricoes(): HasMany
    {
        return $this->hasMany(Inscricao::class, 'codigoEdital');
    }    
}
