<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use App\Models\MenuGroup;
use App\Models\MenuItem;
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
        if (!$this->codigoDelete) {
            return;
        }

        $menu = MenuItem::findOrFail($this->codigoDelete);

        // Bloqueia exclusão caso o item possua subitens (children)
        if ($menu->children()->exists()) {
            session()->flash('error', 'Não é possível excluir um item de menu que possui subitens vinculados.');
            $this->cancelDelete();
            return;
        }

        $menu->delete();

        session()->flash('success', 'Item de menu excluído com sucesso!');
        $this->cancelDelete();
    }

    public function render()
    {
        $menus = MenuItem::with(['group', 'parent'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('label', 'like', "%{$this->search}%")
                          ->orWhere('route', 'like', "%{$this->search}%")
                          ->orWhere('can_permission', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('menu_group_id')
            ->orderBy('order')
            ->paginate(10);

        return view('admin.menus.index', [
            'menus' => $menus,
        ]);
    }
};
?>

@section('breadcrumbs')
    <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : '#' }}" class="hover:text-gray-700 hover:underline">Dashboard</a>
    <span>/</span>
    <span>Menu</span>
@endsection

<div class="space-y-6">
    <x-portal::page-header
        title="Menu"
        subtitle="Gerenciamento da navegação e permissões do menu lateral.">

        <x-slot:actions>
            <x-portal::button icon="fa-plus" :href="route('admin.menus.create')">Novo</x-portal::button>
            <x-portal::button icon="fa-grip" :href="route('admin.menus.grupos')">Grupo</x-portal::button>
        </x-slot:actions>        
    </x-portal::page-header>

    <x-portal::flash-messages />

    @if(blank($menus))
        <x-portal::alert variant="warning" title=" ">
           Nenhum item de menu cadastrado. Clique no botão Novo para cadastrar um item.
        </x-portal::alert>    
    @else
        <x-portal::card>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:col-span-2">
                <x-portal::input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Buscar por rótulo, rota ou permissão..."
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
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Item</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Grupo</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Rota / URL</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Permissão (can)</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Ordem</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500"></th>
                    </tr>
                </x-slot:head>

                <x-slot:body>
                    @foreach($menus as $menu)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    @if($menu->parent_id)
                                        <span class="text-gray-400 pl-3">└─</span>
                                    @endif
                                    <i class="fa-solid {{ $menu->icon ?? 'fa-solid fa-circle' }} w-4 text-center text-gray-500"></i>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $menu->label }}</p>
                                        @if($menu->is_guest_only)
                                            <span class="text-[10px] text-amber-600 dark:text-amber-400 font-medium">Apenas Visitantes</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                {{ $menu->group?->label ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-xs font-mono text-gray-600 dark:text-gray-400">
                                {{ $menu->route ?? $menu->url ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                @if($menu->can_permission)
                                    @php
                                        $permissoes = array_filter(array_map('trim', explode('|', $menu->can_permission ?? '')));
                                    @endphp

                                    @foreach($permissoes as $permissao)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                            {{ $permissao }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-xs text-gray-400">Público</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                {{ $menu->order }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                @if($menu->is_active)
                                    <x-portal::badge variant="success">Ativo</x-portal::badge>
                                @else
                                    <x-portal::badge variant="secondary">Inativo</x-portal::badge>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <x-portal::resource-actions
                                    mode="icon"
                                    :editHref="route('admin.menus.edit', $menu)"
                                    :deleteClick="'confirmDelete('.$menu->id.')'"
                                    editVariant="outline"
                                    deleteVariant="danger"
                                />                                
                            </td>
                        </tr>
                    @endforeach
                </x-slot:body>                
            </x-portal::table>

            @if($menus->total() > 10)
            <x-portal::section-footer muted="true">
                <span>Mostrando {{ $menus->currentPage() }}-{{ $menus->perPage() }} de {{ $menus->total() }} registros</span>
                                    
                <div class="flex items-center gap-2">
                    {{ $menus->links() }}
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
                        Excluir Item de Menu
                    </h3>

                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                        Tem certeza que deseja excluir esse item de menu?
                    </p>
                </div>

                <!-- Mensagem de Aviso -->
                <div class="items-center bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 rounded-xl p-3 mb-6 text-xs text-amber-800 dark:text-amber-300 flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-info mt-0.5 pr-2 text-yellow-600 dark:text-yellow-400 text-sm"></i>
                    <div class="ml-1">
                        Esta ação removerá permanentemente o item de menu.
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