<?php

use Livewire\Component;
use Uspdev\Replicado\Graduacao;
use Uspdev\Replicado\Posgraduacao;

new class extends Component
{
    protected function rules()
    {
        return 
        [
            'codigoCurso' => 'required',
            'tituloProjeto' => 'required|string|max:255',
            'descricaoProjeto' => 'required',
            'numeroHorasProjeto' => 'required',
            'periodoProjeto' => 'required',
            'informacoesProjeto' => 'required'
        ];
    }

    protected function messages()
    {
        return 
        [
            'codigoCurso.required' => 'Curso é obrigatório.',
            'tituloProjeto.required' => 'Projeto é obrigatório.',            
            'descricaoProjeto.required' => 'Descrição do Projeto é obrigatório.',
            'numeroHorasProjeto.required' => 'Número de Horas é obrigatório.',
            'periodoProjeto.required' => 'Período é obrigatório.',
            'informacoesProjeto.required' => 'Informações do Projeto é obrigatório.',
        ];
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
            'Pós-Graduação em Engenharia de Materiais'  => 'Pós-Graduação em Engenharia de Materiais',
            'Pós-Graduação em Biotecnologia Industrial' => 'Pós-Graduação em Biotecnologia Industrial',
            'Pós-Graduação em Engenharia Química'       => 'Pós-Graduação em Engenharia Química',
            'Mestrado Profissional Projetos Educacionais de Ciências' => 'Mestrado Profissional Projetos Educacionais de Ciências',
            'Pós-Graduação em Meio Ambiente e Desenvolvimento' => 'Pós-Graduação em Meio Ambiente e Desenvolvimento'
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
    <a href="{{ Route::has('admin.projetos') ? route('admin.projetos') : '#' }}" class="hover:text-gray-700 hover:underline">Projetos</a>
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

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">                                
                <x-portal::select
                    label="Curso"
                    name="codigoCurso"
                    :options="[
                            'Engenharia Química'      => 'Engenharia Química', 
                            'Engenharia Bioquímica'   => 'Engenharia Bioquímica', 
                            'Engenharia de Materiais' => 'Engenharia de Materiais', 
                            'Engenharia Ambiental'    => 'Engenharia Ambiental',
                            'Engenharia Física'       => 'Engenharia Física',
                            'Engenharia de Produção'  => 'Engenharia de Produção',
                            'Pós-Graduação em Engenharia de Materiais'  => 'Pós-Graduação em Engenharia de Materiais',
                            'Pós-Graduação em Biotecnologia Industrial' => 'Pós-Graduação em Biotecnologia Industrial',
                            'Pós-Graduação em Engenharia Química'       => 'Pós-Graduação em Engenharia Química',
                            'Mestrado Profissional Projetos Educacionais de Ciências' => 'Mestrado Profissional Projetos Educacionais de Ciências',
                            'Pós-Graduação em Meio Ambiente e Desenvolvimento' => 'Pós-Graduação em Meio Ambiente e Desenvolvimento'
                        ]"
                    required
                />
                
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <x-portal::input label="Número de Horas" name="numeroHorasProjeto" required />

                    <x-portal::input label="Período" name="periodoProjeto" required />
                </div>
            </div>

            <div class="md:col-span-2">
                <x-portal::textarea label="Descrição" name="descricaoProjeto" required></x-portal::textarea>
            </div>
            
            <div class="md:col-span-2">
                <x-portal::textarea label="Informações" name="informacoesProjeto" required></x-portal::textarea>
            </div>            
            
            <div class="md:col-span-2 flex flex-col gap-3 border-t border-gray-200 pt-4 sm:flex-row sm:justify-end dark:border-gray-700">
                <x-portal::button :href="route('admin.projetos')" variant="secondary" full="true">Cancelar</x-portal::button>
                <x-portal::button full="true" icon="fa-save">Salvar</x-portal::button>
            </div>
        </form>
    </x-portal::card>    
</div>