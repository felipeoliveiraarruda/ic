<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;
use App\Models\MenuItem;
use App\Models\MenuGroup;
use Spatie\Permission\Models\Permission;

new class extends Component
{
    // Campos do formulário
    public ?int $menu_group_id      = null;
    public ?int $parent_id          = null;
    public string $label            = '';
    public string $route            = '';
    public string $url              = '';
    public string $icon             = 'fa-solid fa-circle';
    public string $active_pattern   = '';
    public string $can_permission   = '';
    public int $order               = 1;
    public string $target           = '';
    public bool $is_active          = true;
    public bool $is_guest_only      = false;
    public bool $is_external        = false;

    // Armazena as permissões selecionadas nos checkboxes
    public array $selectedPermissions = [];

    protected function rules(): array
    {
        return [
            'menu_group_id'  => 'required|exists:menu_groups,id',
            'parent_id'      => 'nullable|exists:menu_items,id',
            'label'          => 'required|string|max:255',
            'route'          => 'nullable|string|max:255',
            'url'            => 'nullable|string|max:255',
            'icon'           => 'nullable|string|max:255',
            'active_pattern' => 'nullable|string|max:255',
            'can_permission' => 'nullable|string|max:255',
            'order'          => 'required|integer|min:0',
            'target'         => 'nullable|string|max:50',
            'is_active'      => 'boolean',
            'is_guest_only'  => 'boolean',
            'is_external'    => 'boolean',
            'selectedPermissions' => 'nullable|array',
        ];
    }

    // Oculta a mensagem de erro para a regra 'required'
    protected array $messages = [
        'required' => '',
    ];

    public function mount(): void
    {
        // Define o primeiro grupo como padrão, caso exista
        $firstGroup = MenuGroup::where('is_active', true)->orderBy('order')->first();
        if ($firstGroup) {
            $this->menu_group_id = $firstGroup->id;
        }
    }

    public function salvar()
    {
        $this->validate();

        // Converte o array de permissões selecionadas em string separada por vírgulas
        $canPermission = !empty($this->selectedPermissions) 
            ? implode('|', $this->selectedPermissions) 
            : null;        

        MenuItem::create([
            'menu_group_id'  => $this->menu_group_id,
            'parent_id'      => $this->parent_id ?: null,
            'label'          => $this->label,
            'route'          => $this->route ?: null,
            'url'            => $this->url ?: null,
            'icon'           => $this->icon ?: 'fa-solid fa-circle',
            'active_pattern' => $this->active_pattern ?: null,
            'can_permission' => $canPermission,
            'order'          => $this->order,
            'target'         => $this->target ?: null,
            'is_active'      => $this->is_active,
            'is_guest_only'  => $this->is_guest_only,
            'is_external'    => $this->is_external,
        ]);

        session()->flash('success', 'Item de menu criado com sucesso!');

        return redirect()->route('admin.menus.index');
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
    <a href="{{ route('admin.menus') }}" class="hover:text-gray-700 hover:underline">Menu</a>
    <span>/</span>
    <span>Novo</span>
@endsection

<div class="space-y-6">
    <x-portal::flash-messages />

    <!-- Formulário Principal -->
    <form wire:submit.prevent="salvar" class="space-y-6" novalidate>
        @include('admin.menus.partials.form')
    </form>   
</div>