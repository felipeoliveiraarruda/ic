<!-- Card 1: Informações Gerais -->
<x-portal::card>
    <x-slot:header>
        <div class="flex w-full flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3 min-w-0 flex-1">
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-portal-gradient text-white shadow-sm">
                    <i class="fa fa-info-circle text-sm"></i>
                </div>
                <div class="min-w-0">
                    <h2 class="text-base font-semibold text-gray-900 truncate">Informações Gerais</h2>
                    <p class="text-xs text-gray-500">Dados fundamentais da navegação do menu lateral</p>
                </div>
            </div>

            <!-- Botões alinhados à direita -->
            <div class="flex items-center gap-2 flex-shrink-0 sm:ms-auto">
               <x-portal::button type="submit" icon="fa-solid fa-floppy-disk" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">Salvar</span>
                    <span wire:loading wire:target="save">Salvando...</span>
                </x-portal::button>

                <x-portal::button :href="route('admin.menus')" variant="secondary" icon="fa-solid fa-arrow-rotate-left">
                    Cancelar
                </x-portal::button>
            </div>
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-portal::select
            label="Grupo"
            wire:model.live="menu_group_id"
            :options="$this->gruposOptions"
            :error="$errors->has('menu_group_id')"
            required
        />

        <x-portal::select
            label="Item Pai (Submenu opcional)"
            wire:model.live="parent_id"
            :options="$this->itensOptions"
        />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:grid-cols-3">
        <x-portal::input
            wire:model="label"
            label="Rótulo"
            placeholder="Ex: Projetos IC"
            required
        />

        <x-portal::input
            wire:model="route"
            label="Rota (routes/web.php)"
            placeholder="Ex: admin.projetos"
            required
        />

        <x-portal::input
            wire:model="url"
            label="URL Direta (se não houver rota)"
            placeholder="Ex: /admin/projetos"
        />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:grid-cols-3">
        <x-portal::input
            wire:model="icon"
            label="Ícone (FontAwesome)"
            placeholder="Ex: fa-diagram-project"
            required
        />

        <x-portal::input
            wire:model="active_pattern"
            label="Padrão para Item Ativo (Route pattern)"
            placeholder="Ex: admin.projetos.*"
            required
        />

        <x-portal::input
            wire:model="order"
            type="number"
            label="Ordem de Exibição"
            required
        />        
    </div>
    
    <div class="flex flex-wrap items-center gap-6 pt-2 border-t border-gray-100 dark:border-gray-800">
        <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
            <input type="checkbox" wire:model="is_active" class="rounded text-blue-600 border-gray-300 focus:ring-blue-500">
            <span>Ativo</span>
        </label>

        <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
            <input type="checkbox" wire:model="is_guest_only" class="rounded text-blue-600 border-gray-300 focus:ring-blue-500">
            <span>Apenas Visitantes (Não Logados)</span>
        </label>

        <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
            <input type="checkbox" wire:model="is_external" class="rounded text-blue-600 border-gray-300 focus:ring-blue-500">
            <span>Link Externo</span>
        </label>
    </div>
</x-portal::card>

<!-- Card 2: Permissões -->
<x-portal::card>
    <x-slot:header>
        <div class="flex w-full flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3 min-w-0 flex-1">
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-portal-gradient text-white shadow-sm">
                    <i class="fa-solid fa-users-gear text-sm"></i>
                </div>
                <div class="min-w-0">
                    <h2 class="text-base font-semibold text-gray-900 truncate">Permissão / Gate (can)</h2>
                    <p class="text-xs text-gray-500">Permissões para visualização do menu</p>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 gap-4">
        <!-- SELEÇÃO DE PERMISSÕES VIA CHECKBOX -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                Permissões Autorizadas (Gate / can)
            </label>

            <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 max-h-48 overflow-y-auto flex flex-wrap items-center gap-x-4 gap-y-2">
                @forelse($permissions as $permission)
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer hover:bg-gray-200/60 dark:hover:bg-gray-800 px-2 py-1 rounded transition">
                        <input type="checkbox" 
                            wire:model="selectedPermissions" 
                            value="{{ $permission->name }}" 
                            class="rounded text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span>{{ $permission->name }}</span>
                    </label>
                @empty
                    <p class="text-xs text-gray-400 p-1">Nenhuma permissão encontrada na tabela permissions.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-portal::card>