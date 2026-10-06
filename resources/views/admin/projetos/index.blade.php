<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use App\Models\Projeto;
use App\Models\PreRequisito;
use Carbon\Carbon;

new class extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Url(except: '')]
    public string $search = '';

    public bool $showDeleteModal = false;
    public ?int $codigoDelete = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function cleanFilters(): void
    {
        $this->reset('search');
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->codigoDelete = $id;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->codigoDelete = null;
    }

    public function excluir(?int $id = null): void
    {
        if (!$this->codigoDelete) 
        {
            return;
        }

        $projeto = Projeto::findOrFail($this->codigoDelete);

        if ($projeto->editais()->exists()) 
        {
            session()->flash('error', 'Não é possível excluir projetos vinculados a editais.');
            $this->cancelDelete();
            return;
        }

        // Remove relacionamentos de cursos vinculados e deleta o projeto
        $projeto->cursos()->delete();
        $projeto->delete();

        session()->flash('success', 'Projeto excluído com sucesso!');
        $this->cancelDelete();
    } 
    
    public function render()
    {
        $projetos = Projeto::with(['editais', 'cursos'])
                    ->when($this->search, fn($q) => $q->where('tituloProjeto', 'like', "%{$this->search}%"))
                    ->latest()
                    ->paginate(10);

        return view('admin.projetos.index',
        [
            'projetos'  => $projetos,
            'hoje'      => Carbon::today()
        ]);            
    }
}
?>

@section('breadcrumbs')
    <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : '#' }}" class="hover:text-gray-700 hover:underline">Dashboard</a>
    <span>/</span>
    <span>Projeto</span>
@endsection

<div class="space-y-6">
    <x-portal::page-header
        title="Projetos"
        subtitle="Visão operacional dos projetos cadastrados.">

       <x-slot:actions>
            <x-portal::button icon="fa-plus" :href="route('admin.projetos.create')">Novo</x-portal::button>
        </x-slot:actions>        
    </x-portal::page-header>

    <x-portal::flash-messages />

    @if(blank($projetos))
        <x-portal::alert variant="warning" title=" ">
           Nenhum projeto cadastrado. Clique no botão Novo para cadastrar um projeto
        </x-portal::alert>    
    @else
        <x-portal::card>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:col-span-2">
                <x-portal::input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Buscar projeto por título..."
                    wrapperClass="mb-0"
                />

                <div class="flex flex-col justify-end">
                    <x-portal::button full="true" variant="secondary" icon="fa-eraser" click="cleanFilters">Limpar filtros</x-portal::button>
                </div>
            </div>
        </x-portal::card>

        <x-portal::card padding="false">
            <x-portal::table>
                <x-slot:head>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Projeto</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Responsável</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Externo?</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500"></th>
                    </tr>
                </x-slot:head>

                <x-slot:body>
                    @foreach($projetos as $projeto)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40">
                            <td class="px-4 py-3">
                                <div class="flex items-start gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $projeto->tituloProjeto }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Inscrições: 
                                            {{ $projeto->editais->first()?->dataInicioEdital ? \Carbon\Carbon::parse($projeto->editais->first()->dataInicioEdital)->format('d/m/Y') : '-' }} - 
                                            {{ $projeto->editais->first()?->dataTerminoEdital ? \Carbon\Carbon::parse($projeto->editais->first()->dataTerminoEdital)->format('d/m/Y') : '-' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $projeto->nomePessoaResponsavel }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                {{ ($projeto->statusExternoProjeto == 'S' ? 'Sim' : 'Não') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                <x-portal::badge variant="success">Ativo</x-portal::badge>
                                <x-portal::badge variant="warning">Manutenção</x-portal::badge>
                                <x-portal::badge variant="info">Atendimento</x-portal::badge>
                                <x-portal::badge variant="secondary">Financeiro</x-portal::badge>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <x-portal::resource-actions
                                    :only="$projeto->editais->isEmpty() ? 'view,edit,delete' : 'view,edit'"
                                    mode="icon"
                                    :viewHref="route('admin.projetos.show', $projeto)"
                                    :editHref="route('admin.projetos.edit', $projeto)"
                                    :deleteClick="'confirmDelete('.$projeto->id.')'"
                                    viewVariant="ghost"
                                    editVariant="outline"
                                    deleteVariant="danger"
                                />                                
                            </td>
                        </tr>

                    @endforeach
                </x-slot:body>                
            </x-portal::table>

            @if($projetos->total() > 10)
            <x-portal::section-footer muted="true">
                <span>Mostrando {{ $projetos->currentPage() }}-{{ $projetos->perPage() }} de {{ $projetos->total() }} registros</span>
                                    
                <div class="flex items-center gap-2">
                    {{ $projetos->links() }}
                </div>
            </x-portal::section-footer>
            @endif            
        </x-portal::card>
    @endif

    <!-- ========================================================= -->
    <!-- MODAL DE CONFIRMAÇÃO DE EXCLUSÃO                          -->
    <!-- ========================================================= -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-all"
             x-data
             x-on:keydown.escape.window="$wire.cancelDelete()">
            
            <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl max-w-md w-full p-6 border border-slate-100 dark:border-slate-800 transform transition-all animate-in fade-in zoom-in duration-200">
                
                <!-- Ícone Alerta -->
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 flex items-center justify-center text-red-600 dark:text-red-400 ring-8 ring-red-50 dark:ring-red-950/20">
                        <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                    </div>

                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">
                        Excluir Projeto
                    </h3>

                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                        Tem certeza que deseja excluir esse projeto?
                    </p>
                </div>

                <!-- Mensagem de Aviso -->
                <div class=" items-center bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 rounded-xl p-3 mb-6 text-xs text-amber-800 dark:text-amber-300 flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-info mt-0.5 pr-2 text-yellow-600 dark:text-yellow-400 text-sm"></i>
                    <div class="ml-1">
                        Esta ação removerá permanentemente o projeto.
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="flex items-center justify-end gap-3">
                    <x-portal::button type="button" variant="secondary" click="cancelDelete">
                        Não
                    </x-portal::button>

                    <button wire:click="excluir" 
                            type="button" 
                            wire:loading.attr="disabled"
                            class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-medium text-sm transition flex items-center gap-2 shadow-md shadow-red-500/20 disabled:opacity-50">
                        <span wire:loading.remove wire:target="excluir" class="flex items-center gap-2">
                            Sim
                        </span>
                        <span wire:loading wire:target="excluir" class="flex items-center gap-2">
                            <i class="fa-solid fa-spinner fa-spin"></i> Excluindo...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>