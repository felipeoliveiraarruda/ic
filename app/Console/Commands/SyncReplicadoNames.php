<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Projeto;
use Illuminate\Support\Facades\DB;
use Uspdev\Replicado\Pessoa;
use Uspdev\Replicado\Graduacao;

class SyncReplicadoNames extends Command
{
    protected $signature = 'sync:replicado-names';
    protected $description = 'Sincroniza os nomes de docentes e cursos do Replicado para o banco de dados local';

    public function handle()
    {
        $this->info('Sincronizando nomes de docentes...');

        $projetos = Projeto::whereNotNull('codigoPessoaResponsavel')->get();
        $this->output->progressStart($projetos->count());

        foreach ($projetos as $projeto) 
        {
            try 
            {
                $nomeDocente = Pessoa::obterNome($projeto->codigoPessoaResponsavel);
                
                if ($nomeDocente) 
                {

                    DB::table('projetos')
                        ->where('id', $projeto->id)
                        ->update(['nomePessoaResponsavel' => $nomeDocente]);
                }
            } 
            catch (\Throwable $e) 
            {
                $this->error("Erro no docente {$projeto->codigoPessoaResponsavel}: {$e->getMessage()}");
            }
            
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        $this->info('Docentes atualizados!');

        $this->info('Sincronizando nomes de cursos...');

        $cursos = DB::table('cursos')->whereNotNull('codigoCurso')->get();
        $this->output->progressStart($cursos->count());

        foreach ($cursos as $curso) 
        {
            try 
            {
                $nomeCurso = Graduacao::nomeCurso($curso->codigoCurso);
            
                if ($nomeCurso) 
                {
                    DB::table('cursos')
                        ->where('id', $curso->id)
                        ->update(['nomeCurso' => $nomeCurso]);
                }
            } 
            catch (\Throwable $e) 
            {
                $this->error("Erro no curso {$curso->codigoCurso}: {$e->getMessage()}");
            }
            
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        $this->info('Sincronização concluída com sucesso!');
    }
}