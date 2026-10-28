<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\MenuItem;
use App\Models\MenuGroup;
use Spatie\Permission\Models\Permission;

new class extends Component
{
    public MenuItem $menu;

    public ?int $menu_group_id = null;
    public ?int $parent_id = null;
    public string $label = '';
    public string $route = '';
    public string $url = '';
    public string $icon = '';
    public string $active_pattern = '';
    
    // Armazena as permissões selecionadas nos checkboxes
    public array $selectedPermissions = [];

    public int $order = 1;
    public string $target = '';
    public bool $is_active = true;
    public bool $is_guest_only = false;
    public bool $is_external = false;

    public function mount(MenuItem $menu): void
    {
        $this->menu           = $menu;
        $this->menu_group_id  = $menu->menu_group_id;
        $this->parent_id      = $menu->parent_id;
        $this->label          = $menu->label;
        $this->route          = $menu->route ?? '';
        $this->url            = $menu->url ?? '';
        $this->icon           = $menu->icon ?? 'fa-solid fa-circle';
        $this->active_pattern = $menu->active_pattern ?? '';
        
        // Converte a string "permissao1,permissao2" para o array dos checkboxes
        $this->selectedPermissions = !empty($menu->can_permission)
            ? array_map('trim', preg_split('/[,|]/', $menu->can_permission))
            : [];

        $this->order         = $menu->order;
        $this->target        = $menu->target ?? '';
        $this->is_active     = (bool) $menu->is_active;
        $this->is_guest_only = (bool) $menu->is_guest_only;
        $this->is_external   = (bool) $menu->is_external;
    }

    protected function rules(): array
    {
        return [
            'menu_group_id'       => 'required|exists:menu_groups,id',
            'parent_id'           => 'nullable|exists:menu_items,id',
            'label'               => 'required|string|max:255',
            'route'               => 'nullable|string|max:255',
            'url'                 => 'nullable|string|max:255',
            'icon'                => 'nullable|string|max:255',
            'active_pattern'      => 'nullable|string|max:255',
            'selectedPermissions' => 'nullable|array',
            'order'               => 'required|integer|min:0',
            'target'              => 'nullable|string|max:50',
            'is_active'           => 'boolean',
            'is_guest_only'       => 'boolean',
            'is_external'         => 'boolean',
        ];
    }

    // Suprime a mensagem de texto das validações required
    protected array $messages = [
        'required' => '',
    ];

    public function salvar()
    {
        $this->validate();

        $canPermission = !empty($this->selectedPermissions) 
            ? implode('|', $this->selectedPermissions) 
            : null;

        $this->menu->update([
            'menu_group_id'  => $this->menu_group_id,
            'parent_id'      => $this->parent_id ?: null,
            'label'          => trim($this->label),
            'route'          => trim($this->route) ?: null,
            'url'            => trim($this->url) ?: null,
            'icon'           => trim($this->icon) ?: 'fa-solid fa-circle',
            'active_pattern' => trim($this->active_pattern) ?: null,
            'can_permission' => $canPermission,
            'order'          => $this->order,
            'target'         => trim($this->target) ?: null,
            'is_active'      => $this->is_active,
            'is_guest_only'  => $this->is_guest_only,
            'is_external'    => $this->is_external,
        ]);

        session()->flash('success', 'Menu atualizado com sucesso!');

        return redirect()->route('admin.menus');
    }

    #[Computed]
    public function gruposOptions(): array
    {
        $options = [];
               
        $grupos = MenuGroup::where('is_active', true)->orderBy('order')->get();
                
        foreach ($grupos as $grupo) 
        {
            $options[$grupo->id] = $grupo->label;
        }

        return $options;
    }

    #[Computed]
    public function itensOptions(): array
    {
        $options = [];
               
        $itens = MenuItem::whereNull('parent_id')->orderBy('label')->get();
                
        foreach ($itens as $item) 
        {
            $options[$item->id] = $item->label;
        }

        return $options;
    }        

    public function render()
    {
        return view('admin.menus.edit', 
        [
            'itens'         => MenuItem::whereNull('parent_id')->orderBy('label')->get(),
            'permissions'   => Permission::whereIn('id', [1,2,3,4,5,6,7,8,9,10,11,12,13,22])->get(),
        ]);
    }
}
?>

@section('breadcrumbs')
    <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : '#' }}" class="hover:text-gray-700 hover:underline">Dashboard</a>
    <span>/</span>
    <a href="{{ route('admin.menus') }}" class="hover:text-gray-700 hover:underline">Itens de Menu</a>
    <span>/</span>
    <span>Editar</span>
@endsection

<div class="space-y-6">
    <x-portal::page-header
        title="Editar Menu"
        subtitle="Altere as configurações e permissões do item '{{ $menu->label }}'.">

        <x-slot:actions>
            <x-portal::button variant="secondary" icon="fa-arrow-left" :href="route('admin.menus')">Voltar</x-portal::button>
        </x-slot:actions>
    </x-portal::page-header>

    <x-portal::flash-messages />

    <!-- Formulário Principal -->
    <form wire:submit.prevent="salvar" class="space-y-6" novalidate>
        @include('admin.menus.partials.form')
    </form>
</div>