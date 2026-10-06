<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Projeto;
use Uspdev\Replicado\Graduacao;
use Uspdev\Replicado\Pessoa;

new class extends Component
{
    // Propriedades do formulário
    public $tituloProjeto;
    public $codigoPessoa;
    public $descricaoProjeto;
    public $codigoCurso;
    public $linhaPesquisaProjeto;
    public $periodoProjeto;
    public $statusExternoProjeto = 'N';
    public $tipoBolsaProjeto;
    public $bolsaProjeto;
    public $dataInicioProjeto;
    public $dataTerminoProjeto;
    public $informacoesProjeto;

    // Limpa o campo de texto caso a opção selecionada deixe de ser 'Com Bolsa'
    public function updatedTipoBolsaProjeto($value)
    {
        if ($value !== 'Com Bolsa') {
            $this->bolsaProjeto = null;
        }
    }
    protected function rules()
    {
        return [
            'tituloProjeto'        => 'required|string|max:255',
            'codigoPessoa'         => 'required',
            'descricaoProjeto'     => 'required|string',
            'codigoCurso'          => 'required|string',
            'linhaPesquisaProjeto' => 'required|string',
            'periodoProjeto'       => 'required|string',
            'statusExternoProjeto' => 'required|in:S,N',
            'tipoBolsaProjeto'     => 'required|string',
            'bolsaProjeto'         => 'nullable|string',
            'dataInicioProjeto'    => 'required|date',
            'dataTerminoProjeto'   => 'required|date|after_or_equal:dataInicioProjeto',
            'informacoesProjeto'   => 'nullable|string',
        ];
    }

    protected function messages()
    {
        return [
            'tituloProjeto.required'        => 'O título do projeto é obrigatório.',
            'codigoPessoa.required'         => 'O docente é obrigatório',
            'descricaoProjeto.required'     => 'A descrição do projeto é obrigatória.',
            'codigoCurso.required'          => 'O curso é obrigatório.',
            'linhaPesquisaProjeto.required' => 'A linha de pesquisa é obrigatória.',
            'periodoProjeto.required'       => 'O período é obrigatório.',
            'statusExternoProjeto.required' => 'Informe se aceita alunos externos.',
            'tipoBolsaProjeto.required'     => 'Informe a situação da bolsa de estudo.',
            'dataInicioProjeto.required'    => 'A data de início é obrigatória.',
            'dataInicioProjeto.date'        => 'Informe uma data válida.',
            'dataTerminoProjeto.required'   => 'A data de término é obrigatória.',
            'dataTerminoProjeto.date'       => 'Informe uma data válida.',
            'dataTerminoProjeto.after_or_equal' => 'A data de término deve ser igual ou posterior à data de início.',
        ];
    }


    /*<select id="comboCurso" style="margin-bottom: 15px;" class="validate[required]" zn_id="63">
        <option value="" zn_id="125"></option>
        <option value="88251|0" zn_id="126">Engenharia Ambiental - integral</option>
        <option value="88152|0" zn_id="127">Engenharia Bioquímica - integral</option>
        <option value="88202|0" zn_id="128">Engenharia de Materiais - integral</option>
        <option value="88352|4" zn_id="129">Engenharia de Produção - noturno</option>
        <option value="88301|0" zn_id="130">Engenharia Física - integral</option>
        <option value="88052|0" zn_id="131">Engenharia Química - integral</option>
        <option value="88052|4" zn_id="132">Engenharia Química - noturno</option>
    </select>*/

    public function mount()
    {
        if (Auth::check()) 
        {
            $cursos = Graduacao::obterCursosHabilitacoes(88);
            dd($cursos);

            $user = Auth::user();
            $codpes = $user->codpes ?? $user->id ?? null;

            $level = session('level');
            $vinculos = session('vinculos', []);

            // Verifica se o usuário tem vínculo de Docente
            $isDocente = Arr::exists($vinculos, 'Docente') || in_array('Docente', $vinculos);

            if ($isDocente && $codpes) 
            {
                $this->docentesOptions = (string) $codpes;
            }
        }
    }

    /**
     * Propriedade computada que carrega os docentes do Replicado USP.
     * Retorna um array associativo no formato ['codpes' => 'Nome do Docente']
     */
    #[Computed]
    public function docentesOptions(): array
    {
        $options = [];

        try {
            // Busca os docentes ativos no Replicado (da unidade configurada no .env)
            $docentes = Pessoa::listarDocentes();

            if (is_array($docentes)) {
                
                // Ordena alfabeticamente pelo nome do docente
                usort($docentes, fn($a, $b) => strcmp($a['nompes'] ?? '', $b['nompes'] ?? ''));

                foreach ($docentes as $docente) {
                    if (isset($docente['codpes'], $docente['nompes'])) {
                        $options[$docente['codpes']] = $docente['nompes'];
                    }
                }
            }
        } catch (\Throwable $e) {
            // Silencia exceções do Replicado para evitar quebrar a página se o banco estiver indisponível
        }

        return $options;
    }

    public function save()
    {
        $validatedData = $this->validate();

        // Adiciona o código da pessoa (docente logado)
        $validatedData['codigoPessoaCriacao'] = auth()->user()->codpes ?? auth()->user()->id;
        $validatedData['codigoPessoaAlteracao'] = auth()->user()->codpes ?? auth()->user()->id;

        Projeto::create($validatedData);

        session()->flash('success', 'Projeto cadastrado com sucesso!');

        return redirect()->route('admin.projetos');
    }

    public function render()
    {


        $cursos = [
            'Engenharia Química'      => 'Engenharia Química', 
            'Engenharia Bioquímica'   => 'Engenharia Bioquímica', 
            'Engenharia de Materiais' => 'Engenharia de Materiais', 
            'Engenharia Ambiental'    => 'Engenharia Ambiental',
            'Engenharia Física'       => 'Engenharia Física',
            'Engenharia de Produção'  => 'Engenharia de Produção',
        ];

        return view('pages.projetos.create',
        [
            'cursos' => $cursos,
        ]);
    }
};
?>

@section('breadcrumbs')
    <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : '#' }}" class="hover:text-gray-700 hover:underline">Dashboard</a>
    <span>/</span>
    <a href="{{ Route::has('admin.projetos') ? route('admin.projetos') : '#' }}" class="hover:text-gray-700 hover:underline">Projeto</a>
    <span>/</span>
    <span>Novo Projeto</span>
@endsection

<div class="space-y-6">
    <x-portal::page-header
        title="Novo Projeto"
        subtitle="Formulário de cadastro do Projeto">
    </x-portal::page-header>

    <x-portal::card padding="false">
        <form wire:submit.prevent="save" class="grid grid-cols-1 gap-4 p-6 md:grid">
                <x-portal::button :href="route('admin.projetos')" variant="secondary" full="true">Cancelar</x-portal::button>
                <x-portal::button type="submit" full="true" icon="fa-save">Salvar</x-portal::button>
            </div>
        </form>
    </x-portal::card>    
</div>