<?php

use Livewire\Component;
use App\Models\Projeto;
use App\Models\Interesse;
use Carbon\Carbon;

new class extends Component
{
    public Projeto $projeto;

    public function mount(Projeto $projeto)
    {
        $this->projeto = $projeto;
        
        // Carrega o projeto pelo ID passado na rota
        //$this->projeto = Projeto::with(['editais', 'cursos'])->findOrFail($id);
    }

    public function render()
    {
        return view('admin.projetos.show');
    }
};
?>

@section('breadcrumbs')
    <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : '#' }}" class="hover:text-gray-700 hover:underline">Dashboard</a>
    <span>/</span>
    <a href="{{ Route::has('admin.projetos') ? route('admin.projetos') : '#' }}" class="hover:text-gray-700 hover:underline">Projetos</a>
    <span>/</span>
    <span>ID # {{ $projeto->id }}</span>
@endsection

<div class="space-y-6">
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <x-portal::card class="mb-0">
            <x-slot:header>
                <!-- Adicionado 'w-full' para ocupar toda a largura do card -->
                <div class="flex w-full flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    
                    <!-- Lado Esquerdo: Ícone + Título e ID -->
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-portal-gradient text-white shadow-sm">
                            <i class="fa fa-tag text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <h2 class="text-base font-semibold text-gray-900 truncate">
                                {{ $projeto->tituloProjeto }}
                            </h2>
                            <p class="text-xs text-gray-500">ID # {{ $projeto->id }}</p>
                        </div>
                    </div>                       
                </div>

                <!-- Botões alinhados à direita -->
                <div class="flex items-center gap-2 flex-shrink-0 sm:ms-auto">
                    <x-portal::button :href="route('admin.projetos')" variant="secondary" icon="fa-solid fa-arrow-rotate-left">
                        Voltar
                    </x-portal::button>
                </div>
            </x-slot:header>

            <dl class="space-y-4">
                <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                    <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Docente</dt>
                    <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->nomePessoaResponsavel }}</dd>
                </div>

                <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                    <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Curso</dt>
                    <dd class="md:col-span-2 text-sm text-gray-800">
                        @forelse($projeto->cursos as $curso)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                {{ $curso->nomeCurso }}
                            </span>
                        @empty
                            <span class="text-xs text-gray-400 italic">Não informada</span>
                        @endforelse
                    </dd>
                </div>                    
                
                <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                    <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Descrição</dt>
                    <dd class="md:col-span-2 text-sm text-gray-800 text-justify">{{ $projeto->descricaoProjeto }}</dd>
                </div>

                <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                    <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Período</dt>
                    <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->dataInicioProjeto->format('d/m/Y') }} - {{ $projeto->dataTerminoProjeto->format('d/m/Y') }}</dd>
                </div>                    

                <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                    <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Inscrições</dt>
                    <dd class="md:col-span-2 text-sm text-gray-800">
                        {{ $projeto->editais->first()?->dataInicioEdital ? \Carbon\Carbon::parse($projeto->editais->first()->dataInicioEdital)->format('d/m/Y') : '-' }} - 
                        {{ $projeto->editais->first()?->dataTerminoEdital ? \Carbon\Carbon::parse($projeto->editais->first()->dataTerminoEdital)->format('d/m/Y') : '-' }}                        
                    </dd>
                </div>                         

                <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                    <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Linha de Pesquisa</dt>
                    <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->linhaPesquisaProjeto }}</dd>
                </div>

                <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                    <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Informações</dt>
                    <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->informacoesProjeto }}</dd>
                </div>

                <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                    <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Pré-requisitos</dt>
                    <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->preRequisitosProjeto }}</dd>
                </div>

                <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                    <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Bolsa de Estudo</dt>
                    <dd class="md:col-span-2 text-sm text-gray-800">{{ ($projeto->tipoBolsaProjeto == 'Com Bolsa' ? $projeto->tipoBolsaProjeto.' - '.$projeto->bolsaProjeto : $projeto->tipoBolsaProjeto) }}</dd>
                </div>

                <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                    <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Aceita Alunos Externo a USP?</dt>
                    <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->statusExternoProjeto == 'S' ? 'Sim' : 'Não' }}</dd>
                </div>                     
            </dl>     
        </x-portal::card>
    </div>
</div>