<?php

namespace App\Jobs;

use App\Mail\InscricaoProjetoMail;
use App\Models\Inscricao;
use App\Models\Projeto;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Uspdev\Replicado\Pessoa;

class InscricaoProjetoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Número de tentativas de reenvio em caso de falha.
     */
    public int $tries = 3;

    /**
     * Tempo de espera (em segundos) antes de tentar novamente.
     */
    public int $backoff = 60;

    public function __construct(public Projeto $projeto, public Inscricao $inscricao) 
    {

    }

    public function handle(): void
    {
        $emailResponsavel = null;

        // Tenta obter o e-mail do docente responsável via Replicado USP
        if (!empty($this->projeto->codigoPessoaResponsavel)) 
        {
            try 
            {
                if (class_exists(Pessoa::class)) {
                    $emailResponsavel = Pessoa::obterEmail($this->projeto->codigoPessoaResponsavel);
                }
            } 
            catch (\Throwable $e) 
            {
                logger()->warning("Não foi possível obter o e-mail do Replicado para a pessoa: {$this->projeto->codigoPessoaResponsavel}");
            }
        }

        //$emailDestinatario = $emailResponsavel ?: config('mail.from.address');
        $emailDestinatario = 'dev.ci.eel@usp.br';

        if ($emailDestinatario) 
        {
            Mail::to($emailDestinatario)->send(new InscricaoProjetoMail($this->projeto, $this->inscricao));
        }
    }
}