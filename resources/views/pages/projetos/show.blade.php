<?php

use Livewire\Component;
use App\Models\Projeto;

new class extends Component
{   
    public $id;
    public $projeto;

    public function mount($id)
    {        
        $this->projeto = Projeto::find($id);
    }

    public function render()
    {
        return view('pages.projetos.show',
        [
            'projeto' => $this->projeto,
            'level'   => session('level'),
        ]);
    }
};
?>

@section('breadcrumbs')
    <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : '#' }}" class="hover:text-gray-700 hover:underline">Dashboard</a>
    <span>/</span>
    <a href="{{ Route::has('admin.projetos') ? route('admin.projetos') : '#' }}" class="hover:text-gray-700 hover:underline">Projeto</a>
    <span>/</span>
    <span>Projeto # {{ $projeto->id }}</span>
@endsection

<div class="space-y-6">
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <x-portal::card class="mb-0">
            <x-slot:header>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-portal-gradient text-white shadow-sm">
                            <i class="fa fa-tag text-sm"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-gray-900">Projeto</h2>
                            <p class="text-xs text-gray-500">ID # {{ $projeto->id }}</p>
                        </div>
                    </div>
                </div>
            </x-slot:header>

            <dl class="space-y-4">
                <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                    <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Título</dt>
                    <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->tituloProjeto }}</dd>
                </div>

                <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                    <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Descrição</dt>
                    <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->descricaoProjeto }}</dd>
                </div>                

                <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                    <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Curso</dt>
                    <dd class="md:col-span-2 text-sm text-gray-800">
                        <code class="rounded-lg bg-gray-100 px-2 py-1 text-xs">{{ $projeto->codigoCurso ?? '-' }}</code>
                    </dd>
                </div>

                <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                    <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Linha de Pesquisa</dt>
                    <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->linhaPesquisaProjeto }}</dd>
                </div>

                @php
                    $docente = Uspdev\Replicado\Pessoa::obterNome($projeto->codigoPessoa);
                @endphp 

                <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                    <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Docente</dt>
                    <dd class="md:col-span-2 text-sm text-gray-800">{{ $docente }}</dd>
                </div>

                <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                    <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Período</dt>
                    <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->periodoProjeto }}</dd>
                </div>
                
                <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                    <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Duração</dt>
                    <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->dataInicioProjeto->format('d/m/Y') }} - {{ $projeto->dataTerminoProjeto->format('d/m/Y') }}</dd>
                </div>

                <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                    <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Informações</dt>
                    <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->informacoesProjeto }}</dd>
                </div>

                <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                    <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Bolsa de Estudo</dt>
                    <dd class="md:col-span-2 text-sm text-gray-800">                        
                    {{ $projeto->tipoBolsaProjeto == 'Com Bolsa' ? $projeto->tipoBolsaProjeto.' - '.$projeto->bolsaProjeto : $projeto->tipoBolsaProjeto }}</dd>
                </div>                
                
                @if($level == 'admin')
                <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                    <div class="rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-600">
                        <span class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Criado</span>
                        {{ $projeto->created_at->format('d/m/Y H:i') }}
                    </div>

                    <div class="rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-600">
                        <span class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Atualizado</span>
                        {{ $projeto->updated_at->format('d/m/Y H:i') }}
                    </div>
                </div>
                @endif
                
            </dl>            
        </x-portal::card> 
    </div>
</div>