<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Traits\Auditavel;
use Uspdev\Replicado\Replicado as Config;
use \Uspdev\Replicado\DB;

class Curso extends Model
{
    use \Spatie\Permission\Traits\HasRoles;
    use HasFactory, Notifiable, SoftDeletes, Auditavel;

    protected $fillable = [
        'codigoProjeto',
        'codigoCurso',
        'nomeCurso',
        'codigoPessoaCriacao',
        'codigoPessoaAlteracao',
    ];

    public function projeto(): BelongsTo
    {
        return $this->belongsTo(Projeto::class, 'codigoProjeto');
    }

    public static function listarCursos()
    {
        $codundclg = getenv('REPLICADO_CODUNDCLG');

        $query = "SELECT C.codcur, C.nomcur, H.perhab, HW.codhab
                  FROM CURSOGR C JOIN HABILITACAOGR H ON C.codcur = H.codcur
                                 JOIN HABILITATUALWEB HW ON C.codcur = HW.codcur AND H.codhab = HW.codhab
                  WHERE C.codclg IN ({$codundclg}) AND C.dtaatvcur IS NOT NULL AND C.dtadtvcur IS NULL AND
                        H.dtaatvhab IS NOT NULL AND H.dtadtvhab IS NULL AND 
                        YEAR(HW.dtafimval) = YEAR(GETDATE())
                  ORDER BY C.nomcur, H.nomhab ASC;";
    
        /* Utiliza a Conexão do REPLICADO */
        return \Uspdev\Replicado\DB::fetchAll($query);
    }
}
