<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Traits\Auditavel;

class PreRequisito extends Model
{
    use \Spatie\Permission\Traits\HasRoles;
    use HasFactory, Notifiable, SoftDeletes, Auditavel;

    protected $fillable = [
        'codigoProjeto',
        'codigoCurso',
        'periodoRequisito',
        'codigoPessoaCriacao',
        'codigoPessoaAlteracao',
    ];

    public function projeto(): BelongsTo
    {
        return $this->belongsTo(Projeto::class, 'codigoProjeto');
    }
}
