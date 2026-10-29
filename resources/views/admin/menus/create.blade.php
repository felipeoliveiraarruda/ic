<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\MenuItem;
use App\Models\MenuGroup;
use Spatie\Permission\Models\Permission;

new class extends Component
{
    public ?int $menu_group_id = null;
    public ?int $parent_id = null;
    public string $label = '';
    public string $route = '';
    public string $url = '';
    public string $icon = 'fa-solid fa-circle';
    public string $active_pattern = '';
    
    // Armazena as permissões selecionadas nos checkboxes
    public array $selectedPermissions = [];

    public int $order = 1;
    public string $target = '';
    public bool $is_active = true;
    public bool $is_guest_only = false;
    public bool $is_external = false;

    public function mount(): void
    {
        $firstGroup = MenuGroup::where('is_active', true)->orderBy('order')->first();
        
        if ($firstGroup) 
        {
            $this->menu_group_id = $firstGroup->id;
        }
        
        $this->order = (MenuItem::where('menu_group_id', $this->menu_group_id)->max('order') ?? 0) + 1;
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

        MenuItem::create([
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

        session()->flash('success', 'Menu criado com sucesso!');

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

    public function updatedMenuGroupId($value): void
    {
        if ($value) {
            // Busca a maior ordem do grupo selecionado e soma 1 (caso não existam itens, inicia em 1)
            $this->order = (MenuItem::where('menu_group_id', $value)->max('order') ?? 0) + 1;
        }
    }

    public function render()
    {
        return view('admin.menus.create', 
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
    <a href="{{ Route::has('admin.menus') ? route('admin.menus') : '#' }}" class="hover:text-gray-700 hover:underline">Menu</a>
    <span>/</span>
    <span>Novo</span>
@endsection

<div class="space-y-6">
    <x-portal::page-header
        title="Cadastrar Menu"
        subtitle="Preencha os campos abaixo para criar um novo item de navegação.">

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