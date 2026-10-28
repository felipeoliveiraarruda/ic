<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MenuGroup;

new class extends Component
{
    use WithPagination;

    // Controla se a tela está em modo de edição
    public ?int $editing_id = null;

    // Campos do formulário
    public string $key = '';
    public string $label = '';
    public int $order = 1;
    public bool $is_active = true;

    // Controle do Modal de Exclusão
    public bool $showDeleteModal = false;
    public ?int $codigoDelete = null;

    public function mount(): void
    {
        $this->order = (MenuGroup::max('order') ?? 0) + 1;
    }

    protected function rules(): array
    {
        return 
        [
            'key'       => 'required|string|max:255|unique:menu_groups,key,' . ($this->editing_id ?? 'NULL') . ',id',
            'label'     => 'required|string|max:255',
            'order'     => 'required|integer|min:0',
            'is_active' => 'boolean',
        ];
    }

    protected array $messages = 
    [
        'key.required'      => 'Informe a chave identificadora do grupo (ex: admin, publico).',
        'key.unique'        => 'Esta chave já está cadastrada para outro grupo.',
        'label.required'    => 'Informe o rótulo do grupo de menu.',
        'order.required'    => 'Informe o número de ordem.',
    ];

    public function salvar(): void
    {
        $this->validate();

        if ($this->editing_id) 
        {
            $group = MenuGroup::findOrFail($this->editing_id);
            $group->update([
                'key'       => strtolower(trim($this->key)),
                'label'     => trim($this->label),
                'order'     => $this->order,
                'is_active' => $this->is_active,
            ]);

            session()->flash('success', 'Grupo atualizado com sucesso!');
        } 
        else 
        {
            MenuGroup::create([
                'key'       => strtolower(trim($this->key)),
                'label'     => trim($this->label),
                'order'     => $this->order,
                'is_active' => $this->is_active,
            ]);

            session()->flash('success', 'Grupo cadastrado com sucesso!');
        }

        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $group = MenuGroup::findOrFail($id);
        
        $this->editing_id = $group->id;
        $this->key        = $group->key;
        $this->label      = $group->label;
        $this->order      = $group->order;
        $this->is_active  = (bool) $group->is_active;

        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['editing_id', 'key', 'label']);
        $this->is_active = true;
        $this->order     = (MenuGroup::max('order') ?? 0) + 1;
        $this->resetValidation();
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

    public function excluir(): void
    {
        if (!$this->codigoDelete) {
            return;
        }

        $group = MenuGroup::findOrFail($this->codigoDelete);

        // Impede a exclusão caso existam itens de menu vinculados
        if ($group->items()->exists()) 
        {
            session()->flash('error', 'Não é possível excluir um grupo que possui itens de menu vinculados.');
            $this->cancelDelete();
            return;
        }

        $group->delete();

        session()->flash('success', 'Grupo excluído com sucesso!');
        $this->cancelDelete();
        $this->resetForm();
    }

    public function render()
    {
        return view('admin.menus.grupos', 
        [
            'grupos' => MenuGroup::withCount('items')->orderBy('order')->get(),
        ]);
    }
};
?>

@section('breadcrumbs')
    <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : '#' }}" class="hover:text-gray-700 hover:underline">Dashboard</a>
    <span>/</span>
    <a href="{{ route('admin.menus') }}" class="hover:text-gray-700 hover:underline">Menu</a>
    <span>/</span>
    <span>Grupos</span>
@endsection

<div class="space-y-6">
    <x-portal::page-header
        title="Grupo"
        subtitle="Gerencie os agrupamentos do menu lateral do sistema.">   
    </x-portal::page-header>

    <x-portal::flash-messages />

    <!-- ========================================================= -->
    <!-- FORMULÁRIO DE CADASTRO / EDIÇÃO (PARTE SUPERIOR)         -->
    <!-- ========================================================= -->
    <x-portal::card>
        <form wire:submit="salvar" class="space-y-4" novalidate>
            <div class="flex items-center justify-between border-b pb-3 border-gray-100 dark:border-gray-800">
                <h3 class="text-base font-bold text-gray-800 dark:text-gray-200">
                    <i class="fa-solid {{ $editing_id ? 'fa-pen-to-square' : 'fa-plus' }} text-blue-600 mr-1"></i>
                    {{ $editing_id ? 'Editar Grupo' : 'Novo Grupo' }}
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <x-portal::input
                        wire:model="label"
                        label="Rótulo / Nome do Grupo"
                        placeholder="Ex: Administração"
                        required
                        class="{{ $errors->has('label') ? '!border-red-500 focus:!border-red-500 focus:!ring-red-500' : '' }}"
                    />
                </div>

                <div>
                    <x-portal::input
                        wire:model="key"
                        label="Chave Identificadora (sem espaços)"
                        placeholder="Ex: admin"
                        required
                    />
                </div>

                <div>
                    <x-portal::input
                        wire:model="order"
                        type="number"
                        label="Ordem de Exibição"
                        required
                    />
                </div>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-800">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                    <input type="checkbox" wire:model="is_active" class="rounded text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span>Ativo</span>
                </label>

                <div class="flex items-center gap-2 flex-shrink-0 sm:ms-auto">
                    @if($editing_id)
                        <x-portal::button type="button" variant="secondary" click="cancelEdit" icon="fa-solid fa-arrow-rotate-left">
                            Cancelar
                        </x-portal::button>
                    @endif

                    <x-portal::button type="submit" icon="fa-solid fa-floppy-disk" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="salvar">{{ $editing_id ? 'Atualizar' : 'Salvar' }}</span>
                        <span wire:loading wire:target="salvar">Processando...</span>
                    </x-portal::button>
                </div>
            </div>
        </form>
    </x-portal::card>

    <!-- ========================================================= -->
    <!-- TABELA COM A LISTAGEM (PARTE INFERIOR)                   -->
    <!-- ========================================================= -->
    <x-portal::card padding="false">
        <x-portal::table>
            <x-slot:head>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Rótulo / Nome</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Chave (Key)</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Ordem</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Itens Vinculados</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 text-right">Ações</th>
                </tr>
            </x-slot:head>

            <x-slot:body>
                @forelse($grupos as $group)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 {{ $editing_id === $group->id ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}">
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {{ $group->label }}
                        </td>
                        <td class="px-4 py-3 text-xs font-mono text-gray-600 dark:text-gray-400">
                            {{ $group->key }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                            {{ $group->order }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200">
                                {{ $group->items_count }} item(ns)
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($group->is_active)
                                <x-portal::badge variant="success">Ativo</x-portal::badge>
                            @else
                                <x-portal::badge variant="secondary">Inativo</x-portal::badge>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <x-portal::resource-actions
                                mode="icon"
                                :editClick="'edit('.$group->id.')'"
                                :deleteClick="'confirmDelete('.$group->id.')'"
                                editVariant="outline"
                                deleteVariant="danger"
                            />                                
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-400 text-sm">
                            Nenhum grupo de menu cadastrado até o momento.
                        </td>
                    </tr>
                @endforelse
            </x-slot:body>                
        </x-portal::table>
    </x-portal::card>

    <!-- ========================================================= -->
    <!-- MODAL DE CONFIRMAÇÃO DE EXCLUSÃO                          -->
    <!-- ========================================================= -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-all"
             x-data
             x-on:keydown.escape.window="$wire.cancelDelete()">
            
            <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl max-w-md w-full p-6 border border-slate-100 dark:border-slate-800 transform transition-all animate-in fade-in zoom-in duration-200">
                
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 flex items-center justify-center text-red-600 dark:text-red-400 ring-8 ring-red-50 dark:ring-red-950/20">
                        <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                    </div>

                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">
                        Excluir Grupo de Menu
                    </h3>

                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                        Tem certeza que deseja excluir esse grupo?
                    </p>
                </div>

                <div class="items-center bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 rounded-xl p-3 mb-6 text-xs text-amber-800 dark:text-amber-300 flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-info mt-0.5 pr-2 text-yellow-600 dark:text-yellow-400 text-sm"></i>
                    <div class="ml-1">
                        Ação permanente. Grupos com itens associados não podem ser removidos.
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <x-portal::button type="button" variant="secondary" click="cancelDelete">
                        Não
                    </x-portal::button>

                    <button wire:click="excluir" 
                            type="button" 
                            wire:loading.attr="disabled"
                            class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-medium text-sm transition flex items-center gap-2 shadow-md shadow-red-500/20 disabled:opacity-50">
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