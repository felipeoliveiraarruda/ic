<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Projeto;
use App\Models\Curso;
use App\Models\Edital;
use Uspdev\Replicado\Pessoa;
use Uspdev\Replicado\Graduacao;
use Carbon\Carbon;

new class extends Component
{
    public Projeto $projeto;

    // Propriedades do Formulário
    public string $tituloProjeto = '';
    public string $codigoPessoaResponsavel = '';
    public string $descricaoProjeto = '';
    public ?string $informacoesProjeto = null;
    public string $linhaPesquisaProjeto = '';
    public string $dataInicioProjeto = '';
    public string $dataTerminoProjeto = '';
    public int $numeroVagaProjeto = 1;
    public string $tipoBolsaProjeto = '';
    public ?string $bolsaProjeto = null;
    public string $statusExternoProjeto = 'N';
    public array $codigoCurso = [];
    public ?string $outroCursoText = null;
    public string $preRequisitosProjeto = '';
    public string $dataInicioInscricao = '';
    public string $dataTerminoInscricao = '';

    // Listas para exibição
    public array $cursosDisponiveis = [];

    public function mount(Projeto $projeto)
    {
        $this->projeto = $projeto;

        // Carrega as opções de cursos primeiro
        $this->carregarCursos();

        // Preenche os campos do formulário com os dados existentes
        $this->tituloProjeto            = $projeto->tituloProjeto ?? '';
        $this->codigoPessoaResponsavel  = $projeto->codigoPessoaResponsavel ?? '';
        $this->descricaoProjeto         = $projeto->descricaoProjeto ?? '';
        $this->informacoesProjeto       = $projeto->informacoesProjeto;
        $this->linhaPesquisaProjeto     = $projeto->linhaPesquisaProjeto ?? '';
        $this->dataInicioProjeto        = $projeto->dataInicioProjeto ? Carbon::parse($projeto->dataInicioProjeto)->format('Y-m-d') : '';
        $this->dataTerminoProjeto       = $projeto->dataTerminoProjeto ? Carbon::parse($projeto->dataTerminoProjeto)->format('Y-m-d') : '';
        $this->numeroVagaProjeto        = $projeto->numeroVagaProjeto ?? 1;
        $this->tipoBolsaProjeto         = $projeto->tipoBolsaProjeto ?? '';
        $this->bolsaProjeto             = $projeto->bolsaProjeto;
        $this->statusExternoProjeto     = $projeto->statusExternoProjeto ?? 'N';
        $this->preRequisitosProjeto     = $projeto->preRequisitosProjeto ?? '';

        // Carrega dados do Edital vinculado
        $edital = $projeto->edital ?? Edital::where('codigoProjeto', $projeto->id)->first();
        
        if ($edital) 
        {
            $this->dataInicioInscricao  = $edital->dataInicioEdital ? Carbon::parse($edital->dataInicioEdital)->format('Y-m-d') : '';
            $this->dataTerminoInscricao = $edital->dataTerminoEdital ? Carbon::parse($edital->dataTerminoEdital)->format('Y-m-d') : '';
        }

        // Carrega os cursos vinculados ao projeto
        $cursosVinculados = $projeto->cursos;

        foreach ($cursosVinculados as $curso) 
        {
            $this->codigoCurso[] = $curso->codigoCurso;
            
            // Se o curso for "Outro(s)", resgata o nome/especificação gravada
            if (str_contains($curso->codigoCurso, '88999')) 
            {
                $this->outroCursoText = $curso->nomeCurso;
            }
        }
    }

    private function carregarCursos()
    {
        try 
        {               
            $cursosReplicado = Curso::listarCursos();
           
            foreach ($cursosReplicado as $c) 
            {
                if (isset($c['codcur'], $c['nomcur'])) 
                {
                    $this->cursosDisponiveis["{$c['codcur']}|{$c['codhab']}"] = "{$c['nomcur']} ({$c['perhab']})";
                }
            }

            $this->cursosDisponiveis["88999|0"] = "Outro(s)";
        } 
        catch (\Throwable $e) 
        {        
            $cursosLocais = \Illuminate\Support\Facades\DB::table('cursos')->select('codigoCurso', 'nomeCurso')->get();
        
            foreach ($cursosLocais as $c) 
            {
                $this->cursosDisponiveis[$c->codigoCurso] = $c->nomeCurso ?: $c->codigoCurso;
            }
        }
    }

    // Método auxiliar para verificar se a opção '88999' está presente no array
    public function temOutroCurso(): bool
    {
        return collect($this->codigoCurso)->contains(fn($cod) => str_contains((string) $cod, '88999'));
    }    

    // Regras de Validação
    protected function rules(): array
    {
        return [
            'tituloProjeto'            => ['required', 'string', 'max:255'],
            'codigoPessoaResponsavel'  => ['required'],
            'descricaoProjeto'         => ['required', 'string'],
            'informacoesProjeto'       => ['nullable', 'string'],
            'linhaPesquisaProjeto'     => ['required', 'string', 'max:255'],
            'dataInicioProjeto'        => ['required', 'date'],
            'dataTerminoProjeto'       => ['required', 'date', 'after_or_equal:dataInicioProjeto'],
            'numeroVagaProjeto'        => ['required', 'integer', 'min:1'],
            'tipoBolsaProjeto'         => ['required', 'in:Com Bolsa,Sem Bolsa,Possível Bolsa'],
            'bolsaProjeto'             => ['required_if:tipoBolsaProjeto,Com Bolsa', 'nullable', 'string', 'max:255'],
            'statusExternoProjeto'     => ['required', 'in:N,S'],
            'codigoCurso'              => ['required', 'array', 'min:1'],
            'codigoCurso.*'            => ['string'],
            'outroCursoText'           => [$this->temOutroCurso() ? 'required' : 'nullable', 'string', 'max:255'],
            'preRequisitosProjeto'     => ['required', 'string'],
            'dataInicioInscricao'      => ['required', 'date'],
            'dataTerminoInscricao'     => ['required', 'date', 'after_or_equal:dataInicioInscricao'],
        ];
    }

    // Mensagens Personalizadas
    protected function messages(): array
    {
        return [
            'tituloProjeto.required'           => 'O título do projeto é obrigatório.',
            'tituloProjeto.max'                => 'O título deve ter no máximo 255 caracteres.',
            'codigoPessoaResponsavel.required' => 'Selecione o responsável pelo projeto.',
            'descricaoProjeto.required'        => 'A descrição do projeto é obrigatória.',
            'linhaPesquisaProjeto.required'    => 'A linha de pesquisa é obrigatória.',
            'dataInicioProjeto.required'       => 'Informe a data de início do projeto.',
            'dataInicioProjeto.date'           => 'Insira uma data de início válida.',
            'dataTerminoProjeto.required'      => 'Informe a data de término do projeto.',
            'dataTerminoProjeto.date'          => 'Insira uma data de término válida.',
            'dataTerminoProjeto.after_or_equal'=> 'A data de término deve ser igual ou posterior à data de início.',
            'numeroVagaProjeto.required'       => 'Informe o número de vagas.',
            'numeroVagaProjeto.integer'        => 'O número de vagas deve ser um valor inteiro.',
            'numeroVagaProjeto.min'            => 'O número de vagas deve ser no mínimo 1.',
            'tipoBolsaProjeto.required'        => 'Selecione a opção da bolsa.',
            'bolsaProjeto.required_if'         => 'Informe o tipo da bolsa quando a opção "Com Bolsa" for selecionada.',
            'statusExternoProjeto.required'    => 'Informe se o projeto aceita aluno externo à USP.',
            'codigoCurso.required'             => 'Selecione pelo menos um curso desejado.',
            'codigoCurso.min'                  => 'Selecione pelo menos um curso desejado.',
            'outroCursoText.required'          => 'Informe a especificação para a opção "Outro(s)".',
            'outroCursoText.max'               => 'A especificação do curso deve ter no máximo 255 caracteres.',
            'preRequisitosProjeto.required'    => 'Informe os detalhes dos pré-requisitos.',
            'dataInicioInscricao.required'     => 'Informe a data de início de inscrição do projeto.',
            'dataInicioInscricao.date'         => 'Insira uma data de início de inscrição válida.',
            'dataTerminoInscricao.required'    => 'Informe a data de término de inscrição do projeto.',
            'dataTerminoInscricao.date'        => 'Insira uma data de término de inscrição válida.',
            'dataTerminoInscricao.after_or_equal'=> 'A data de término de inscrição deve ser igual ou posterior à data de início.',
        ];
    }

    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);
    }

    public function updatedCodigoCurso(): void
    {
        if (!$this->temOutroCurso()) 
        {
            $this->outroCursoText = null;
            $this->resetErrorBag('outroCursoText');
        }
    }

    public function updatedTipoBolsaProjeto($value): void
    {
        if ($value !== 'Com Bolsa') 
        {
            $this->bolsaProjeto = null;
            $this->resetErrorBag('bolsaProjeto');
        }
    }

    public function save()
    {
        $dados = $this->validate();

        // Obtém o nome do docente no Replicado
        $nomeDocente = null;

        try 
        {
            $nomeDocente = Pessoa::obterNome($this->codigoPessoaResponsavel);
        } 
        catch (\Throwable $e) 
        {
            $nomeDocente = null;
        }

        $dados['nomePessoaResponsavel'] = $nomeDocente;

        // 1. Atualiza o Projeto
        $this->projeto->update($dados);

        // 2. Sincroniza os Cursos Vinculados (sem excluir todos)
        $this->projeto->cursos()
             ->whereNotIn('codigoCurso', $this->codigoCurso)
             ->delete();

        foreach ($this->codigoCurso as $codCurso) 
        {    
            if (str_contains((string) $codCurso, '88999')) 
            {
                $nomeCurso = $this->outroCursoText ?: 'Outro(s)';
            } 
            else 
            {
                $nomeCurso = $this->cursosDisponiveis[$codCurso] ?? null;

                if (!$nomeCurso) 
                {
                    try 
                    {
                        $nomeCurso = Graduacao::nomeCurso($codCurso);
                    } 
                    catch (\Throwable $e) 
                    {
                        $nomeCurso = null;
                    }
                }
            }

            $this->projeto->cursos()->updateOrCreate(
                ['codigoCurso' => $codCurso],
                ['nomeCurso'   => $nomeCurso]
            );
        }

        // 3. Atualiza ou cria o Edital de Inscrição
        Edital::updateOrCreate(
            ['codigoProjeto' => $this->projeto->id],
            [
                'dataInicioEdital'  => $this->dataInicioInscricao,
                'dataTerminoEdital' => $this->dataTerminoInscricao,
            ]
        );

        session()->flash('success', 'Projeto atualizado com sucesso!');
        return redirect()->route('admin.projetos');
    }

    #[Computed]
    public function docentesOptions(): array
    {
        $options = [];
        try 
        {
            $docentes = Pessoa::listarDocentes();
            
            if (is_array($docentes))
            {
                usort($docentes, fn($a, $b) => strcmp($a['nompes'] ?? '', $b['nompes'] ?? ''));
            
                foreach ($docentes as $docente) 
                {
                    if (isset($docente['codpes'], $docente['nompes'])) 
                    {
                        $options[$docente['codpes']] = $docente['nompes'] . ' (' . $docente['codpes'] . ')';
                    }
                }
            }
        } 
        catch (\Throwable $e) {}

        return $options;
    }

    public function render()
    {
        return view('admin.projetos.edit');
    }
};
?>

@section('breadcrumbs')
    <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : '#' }}" class="hover:text-gray-700 hover:underline">Dashboard</a>
    <span>/</span>
    <a href="{{ Route::has('admin.projetos') ? route('admin.projetos') : '#' }}" class="hover:text-gray-700 hover:underline">Projetos</a>
    <span>/</span>
    <span>Editar</span>
@endsection

<div class="space-y-6">
    <!-- Cabeçalho Principal com Gradiente -->
    <div class="relative z-10 p-6 mb-6 rounded-2xl shadow-lg overflow-hidden bg-portal-gradient">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                    <i class="fa-solid fa-pen-to-square text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white mb-1">
                        Editar Projeto de Iniciação Científica
                    </h1>
                    <p class="text-white/80 text-sm">
                        Atualize as informações da oferta de projeto #{{ $projeto->id }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário Principal -->
    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        @include('admin.projetos.partials.form')
    </form>
</div>