<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use App\Traits\Auditavel;

class Inscricao extends Model
{
    use \Spatie\Permission\Traits\HasRoles;
    use HasFactory, Notifiable, SoftDeletes, Auditavel;

     protected $table = 'inscricoes';

    protected $fillable = [
        'codigoEdital',
        'codigoUsuario',
        'cursoInscricao',
        'instituicaoInscricao',
        'historicoEscolarInscricao',
        'codigoUsuarioCriacao',
        'codigoUsuarioAlteracao',
    ];

    public function editais(): BelongsTo
    {
        return $this->belongsTo(Edital::class, 'codigoEdital');
    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'codigoUsuario');
    }
}
