<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use App\Models\Projeto;
use Uspdev\Replicado\Graduacao;
use Uspdev\Replicado\Pessoa;

new class extends Component
{
    // Instância do projeto a ser editado
    public Projeto $projeto;

    // Propriedades do formulário
    public $tituloProjeto;
    public $codigoPessoa;
    public $descricaoProjeto;
    public $codigoCurso;
    public $linhaPesquisaProjeto;
    public $periodoProjeto;
    public $statusExternoProjeto;
    public $tipoBolsaProjeto;
    public $bolsaProjeto;
    public $dataInicioProjeto;
    public $dataTerminoProjeto;
    public $informacoesProjeto;

    public function mount(Projeto $projeto)
    {
        $this->projeto = $projeto;

        // Preenche as propriedades reativas com os dados do projeto
        $this->fill([
            'tituloProjeto'        => $projeto->tituloProjeto,
            'codigoPessoa'         => $projeto->codigoPessoa,
            'descricaoProjeto'     => $projeto->descricaoProjeto,
            'codigoCurso'          => $projeto->codigoCurso,
            'linhaPesquisaProjeto' => $projeto->linhaPesquisaProjeto,
            'periodoProjeto'       => $projeto->periodoProjeto,
            'statusExternoProjeto' => $projeto->statusExternoProjeto,
            'tipoBolsaProjeto'     => $projeto->tipoBolsaProjeto,
            'bolsaProjeto'         => $projeto->bolsaProjeto,
            // Formata as datas para o padrão do input 'Y-m-d'
            'dataInicioProjeto'    => $projeto->dataInicioProjeto ? substr($projeto->dataInicioProjeto, 0, 10) : null,
            'dataTerminoProjeto'   => $projeto->dataTerminoProjeto ? substr($projeto->dataTerminoProjeto, 0, 10) : null,
            'informacoesProjeto'   => $projeto->informacoesProjeto,
        ]);
    }

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
            'tituloProjeto.required'            => 'O título do projeto é obrigatório.',
            'codigoPessoa.required'             => 'O docente é obrigatório.',
            'descricaoProjeto.required'         => 'A descrição do projeto é obrigatória.',
            'codigoCurso.required'              => 'O curso é obrigatório.',
            'linhaPesquisaProjeto.required'     => 'A linha de pesquisa é obrigatória.',
            'periodoProjeto.required'           => 'O período é obrigatório.',
            'statusExternoProjeto.required'     => 'Informe se aceita alunos externos.',
            'tipoBolsaProjeto.required'         => 'Informe a situação da bolsa de estudo.',
            'dataInicioProjeto.required'        => 'A data de início é obrigatória.',
            'dataInicioProjeto.date'            => 'Informe uma data válida.',
            'dataTerminoProjeto.required'       => 'A data de término é obrigatória.',
            'dataTerminoProjeto.date'           => 'Informe uma data válida.',
            'dataTerminoProjeto.after_or_equal' => 'A data de término deve ser igual ou posterior à data de início.',
        ];
    }

    /**
     * Propriedade computada que carrega os docentes do Replicado USP.
     */
    #[Computed]
    public function docentesOptions(): array
    {
        $options = [];

        try {
            $docentes = Pessoa::listarDocentes();

            if (is_array($docentes)) {
                usort($docentes, fn($a, $b) => strcmp($a['nompes'] ?? '', $b['nompes'] ?? ''));

                foreach ($docentes as $docente) {
                    if (isset($docente['codpes'], $docente['nompes'])) {
                        $options[$docente['codpes']] = $docente['nompes'];
                    }
                }
            }
        } catch (\Throwable $e) {
            // Silencia exceções do Replicado
        }

        // Garante que o docente atual do projeto esteja na lista caso não esteja presente no array retornado
        if ($this->codigoPessoa && !array_key_exists($this->codigoPessoa, $options)) {
            $nome = Pessoa::obterNome($this->codigoPessoa) ?: $this->codigoPessoa;
            $options[$this->codigoPessoa] = $nome;
        }

        return $options;
    }

    public function save()
    {
        $validatedData = $this->validate();

        // Atualiza a informação do usuário que realizou a alteração
        $validatedData['codigoPessoaAlteracao'] = auth()->user()->codpes ?? auth()->user()->id;

        $this->projeto->update($validatedData);

        session()->flash('success', 'Projeto atualizado com sucesso!');

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

        return view('pages.projetos.edit', [
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
    <span>Editar Projeto</span>
@endsection

<div class="space-y-6">
    <x-portal::page-header
        title="Editar Projeto"
        subtitle="Formulário de edição do Projeto">
    </x-portal::page-header>

    <x-portal::card padding="false">
        <form wire:submit.prevent="save" class="grid grid-cols-1 gap-4 p-6 md:grid">

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-portal::input 
                    label="Projeto" 
                    name="tituloProjeto" 
                    wire:model="tituloProjeto" 
                    required 
                />

                <x-portal::select
                    label="Responsável"
                    wire:model="codigoPessoa"
                    :options="$this->docentesOptions"
                    wrapperClass="mb-0"
                    required
                />                
            </div>

            <div class="md:col-span-2">
                <x-portal::textarea 
                    label="Descrição" 
                    name="descricaoProjeto" 
                    wire:model="descricaoProjeto" 
                    required>
                </x-portal::textarea>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <x-portal::select
                    label="Curso"
                    name="codigoCurso"
                    wire:model="codigoCurso"
                    :options="[
                        'Engenharia Química'      => 'Engenharia Química', 
                        'Engenharia Bioquímica'   => 'Engenharia Bioquímica', 
                        'Engenharia de Materiais' => 'Engenharia de Materiais', 
                        'Engenharia Ambiental'    => 'Engenharia Ambiental',
                        'Engenharia Física'       => 'Engenharia Física',
                        'Engenharia de Produção'  => 'Engenharia de Produção'
                    ]"
                    required
                />
                
                <x-portal::input 
                    label="Linha de Pesquisa" 
                    name="linhaPesquisaProjeto" 
                    wire:model="linhaPesquisaProjeto" 
                    required 
                />

                <x-portal::input 
                    label="Período" 
                    name="periodoProjeto" 
                    wire:model="periodoProjeto" 
                    required 
                />

                <x-portal::select
                    label="Aceita Aluno Externo a USP"
                    name="statusExternoProjeto"
                    wire:model="statusExternoProjeto"
                    :options="[
                        'N' => 'Não', 
                        'S' => 'Sim', 
                    ]"
                    required
                />
            </div>            

            <div class="grid grid-cols-1 gap-4 md:grid-cols-4 md:col-span-2">
                <x-portal::select
                    label="Bolsa"
                    name="tipoBolsaProjeto"
                    wire:model.live="tipoBolsaProjeto"
                    :options="[
                        'Com Bolsa'      => 'Com Bolsa', 
                        'Sem Bolsa'      => 'Sem Bolsa', 
                        'Possível Bolsa' => 'Possível Bolsa', 
                    ]"
                    required
                />
                
                <x-portal::input 
                    label="Tipo de Bolsa" 
                    name="bolsaProjeto" 
                    wire:model="bolsaProjeto"
                    :disabled="$tipoBolsaProjeto !== 'Com Bolsa'"
                />

                <x-portal::input 
                    type="date" 
                    label="Início do Projeto" 
                    name="dataInicioProjeto" 
                    wire:model="dataInicioProjeto" 
                    required 
                />

                <x-portal::input 
                    type="date" 
                    label="Término do Projeto" 
                    name="dataTerminoProjeto" 
                    wire:model="dataTerminoProjeto" 
                    required 
                />
            </div>   

            <div class="md:col-span-2">
                <x-portal::textarea 
                    label="Informações" 
                    name="informacoesProjeto" 
                    wire:model="informacoesProjeto">
                </x-portal::textarea>
            </div>
  
            <div class="md:col-span-2 flex flex-col gap-3 border-t border-gray-200 pt-4 sm:flex-row sm:justify-end dark:border-gray-700">
                <x-portal::button :href="route('admin.projetos')" variant="secondary" full="true">Cancelar</x-portal::button>
                <x-portal::button type="submit" full="true" icon="fa-save">Salvar Alterações</x-portal::button>
            </div>
        </form>
    </x-portal::card>    
</div>