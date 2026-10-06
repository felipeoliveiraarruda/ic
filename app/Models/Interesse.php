<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Uspdev\Replicado\Pessoa;

class Interesse extends Model
{
    use \Spatie\Permission\Traits\HasRoles;
    use HasFactory, Notifiable, SoftDeletes, Auditavel;
       
    protected $fillable = [
        'codigoProjeto',
        'nomeInteresse',
        'emailInteresse',
        'notificacoesInteresse',
    ];

    protected $casts = [
        'notificacoesInteresse' => 'boolean',
    ];

    public function projeto(): BelongsTo
    {
        return $this->belongsTo(Projeto::class, 'codigoProjeto');
    }
}
