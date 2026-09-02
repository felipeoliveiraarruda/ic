<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Traits\Auditavel;

class Inscricao extends Model
{
    use \Spatie\Permission\Traits\HasRoles;
    use HasFactory, Notifiable, SoftDeletes, Auditavel;

    protected $fillable = [
        'codigoEdital',
        'cursoInscricao',
        'instituicaoInscricao',
        'historicoEscolarInscricao',
        'codigoPessoaCriacao',
        'codigoPessoaAlteracao',
    ];

    public function edital(): BelongsTo
    {
        return $this->belongsTo(Edital::class, 'codigoEdital');
    }
}
