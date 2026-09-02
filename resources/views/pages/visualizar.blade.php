<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use App\Models\Projeto; // Subsitua pela Model correta do seu projeto
use Uspdev\Replicado\Pessoa;

new class extends Component
{
    public Projeto $projeto;

    public function mount($id)
    {
        // Carrega o projeto pelo ID passado na rota
        $this->projeto = Projeto::findOrFail($id);
    }

    #[Computed]
    public function docente()
    {
        if (!$this->projeto->codigoPessoa) {
            return 'Não informado';
        }

        return Pessoa::obterNome($this->projeto->codigoPessoa);
    }

    public function render()
    {
        return view('pages.visualizar');
    }
}
?>
<div>
    {{-- Banner de Cabeçalho --}}
    <div class="mb-6 rounded-2xl shadow-lg relative overflow-hidden bg-portal-gradient">
        <div class="relative z-10 p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                        <i class="fa fa-layer-group text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-1">
                            Banco de Ofertas - Iniciação Científica EEL/USP
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Conteúdo Principal --}}
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
                                <h2 class="text-base font-semibold text-gray-900">{{ $projeto->tituloProjeto }}</h2>
                                <p class="text-xs text-gray-500">ID # {{ $projeto->id }}</p>
                            </div>
                        </div>
                    </div>
                </x-slot:header>

                <dl class="space-y-4">
                    @auth
                    <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                        <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Descrição</dt>
                        <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->descricaoProjeto }}</dd>
                    </div>
                    @endauth

                    <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                        <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Curso</dt>
                        <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->codigoCurso ?? '-' }}</dd>
                    </div>

                    <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                        <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Linha de Pesquisa</dt>
                        <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->linhaPesquisaProjeto }}</dd>
                    </div>

                    <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                        <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Docente</dt>
                        <dd class="md:col-span-2 text-sm text-gray-800">{{ $this->docente }}</dd>
                    </div>

                    <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                        <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Período</dt>
                        <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->periodoProjeto }}</dd>
                    </div>

                    <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                        <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Aceita Alunos Externo a USP?</dt>
                        <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->statusExternoProjeto == 'S' ? 'Sim' : 'Não' }}</dd>
                    </div>

                    <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                        <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Bolsa de Estudo</dt>
                        <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->tipoBolsaProjeto }}</dd>
                    </div>

                    @auth
                    <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                        <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Informações</dt>
                        <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->informacoesProjeto }}</dd>
                    </div>
                    @endauth
                </dl>     
            </x-portal::card> 

            <x-portal::button full="true" :href="route('admin.projetos.show', ['id' => $projeto->id])" icon="fa-solid fa-check">
                Tenho Interesse
            </x-portal::button>
            
            <x-portal::button full="true" :href="route('home')" variant="secondary" icon="fa-solid fa-arrow-rotate-left">
                Voltar
            </x-portal::button>
        </div>
    </div>
</div>