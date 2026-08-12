<?php

use Livewire\Component;
use Uspdev\Replicado\Graduacao;

new class extends Component
{
    /*protected function rules()
    {
        return 
        [
            'codigoPessoa' => 'required',
            'codigoCurso' => 'required',
            'tituloProjeto' => 'required',
            'descricaoProjeto' => 'required',
            'periodoProjeto' => 'required5',
            'linhaPesquisaProjeto' => 'required',
            'informacoesProjeto' => 'required',
            'dataInicioProjeto' => 'required|date',
            'dataTerminoProjeto' => 'required|date'
        ];
    }

    protected function messages()
    {
        return 
        [
            'codigoCurso.required' => 'Curso é obrigatório.',
            'tituloProjeto.required' => 'Projeto é obrigatório.',            
            'descricaoProjeto.required' => 'Descrição do Projeto é obrigatório.',
            'periodoProjeto.required' => 'Período é obrigatório.',
            'informacoesProjeto.required' => 'Informações do Projeto é obrigatório.',
        ];
    }*/

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
            'cursos' => $cursos
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
        <form class="grid grid-cols-1 gap-4 p-6 md:grid">

            <div class="md:col-span-2">
                <x-portal::input label="Projeto" name="nome" required />
            </div>

            <div class="md:col-span-2">
                <x-portal::textarea label="Descrição" name="descricaoProjeto" required></x-portal::textarea>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <x-portal::select
                    label="Curso"
                    name="codigoCurso"
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
                
                <x-portal::input label="Linha de Pesquisa" name="linhaPesquisaProjeto" required />

                <x-portal::input label="Período" name="periodoProjeto" required />

                <x-portal::select
                    label="Aceita Aluno Externo a USP"
                    name="statusExternoProjeto"
                    :options="[
                            'Não'      => 'Não', 
                            'Sim'   => 'Sim', 
                        ]"
                    required
                />
            </div>            

            <div class="grid grid-cols-1 gap-4 md:grid-cols-4 md:col-span-2">
                <x-portal::select
                    label="Bolsa"
                    name="tipoBolsaProjeto"
                    :options="[
                            'Com Bolsa'      => 'Com Bolsa', 
                            'Sem Bolsa'      => 'Sem Bolsa', 
                            'Possível Bolsa' => 'Possível Bolsa', 
                        ]"
                    required
                />
                
                <x-portal::input label="Tipo de Bolsa" name="bolsaProjeto"  />

                <x-portal::input type="date" label="Início do Projeto" name="dataInicioProjeto" required />

                <x-portal::input type="date" label="Término do Projeto" name="dataTerminoProjeto" required />
            </div>   

            <div class="md:col-span-2">
                <x-portal::textarea label="Informações" name="informacoesProjeto" required></x-portal::textarea>
            </div>
  
            <div class="md:col-span-2 flex flex-col gap-3 border-t border-gray-200 pt-4 sm:flex-row sm:justify-end dark:border-gray-700">
                <x-portal::button :href="route('admin.projetos')" variant="secondary" full="true">Cancelar</x-portal::button>
                <x-portal::button type="submit" full="true" icon="fa-save">Salvar</x-portal::button>
            </div>
        </form>
    </x-portal::card>    
</div>