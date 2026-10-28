<?php
namespace App\Services;

use App\Models\MenuGroup;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

class NavigationService
{
    public function getMenu(): array
    {
        // Carrega apenas itens principais (parent_id null) e seus subitens (children)
        $groups = MenuGroup::with(['items' => function ($query) {
            $query->whereNull('parent_id')
                  ->where('is_active', true)
                  ->with(['children' => function ($subQuery) {
                      $subQuery->where('is_active', true)->orderBy('order', 'asc');
                  }])
                  ->orderBy('order', 'asc');
        }])
        ->where('is_active', true)
        ->orderBy('order', 'asc')
        ->get();

        $filteredGroups = [];

        foreach ($groups as $group) {
            $filteredItems = [];

            foreach ($group->items as $item) {
                if ($this->shouldDisplayItem($item)) {
                    $filteredItems[] = $this->formatItem($item);
                }
            }

            if (!empty($filteredItems)) {
                $filteredGroups[$group->key] = [
                    'label' => $group->label,
                    'items' => $filteredItems,
                ];
            }
        }

        return $filteredGroups;
    }

    private function formatItem(MenuItem $item): array
    {
        // Formata os subitens (children), se existirem
        $children = [];
        if ($item->children->isNotEmpty()) {
            foreach ($item->children as $child) {
                if ($this->shouldDisplayItem($child)) {
                    $children[] = $this->formatItem($child);
                }
            }
        }

        return [
            'label'       => $item->label,
            'route'       => $item->route,
            'url'         => $item->url,
            'icon'        => $item->icon ?? 'fa-circle',
            'active'      => $item->active_pattern,
            
            // IMPORTANTE: Definimos 'can' e 'guest' como null para evitar que o 
            // componente Blade <x-portal::sidebar-item> tente re-validar a string "manager,docente"
            'can'         => null,
            'guest'       => null,
            
            'external'    => $item->is_external,
            'target'      => $item->target,
            'rel'         => $item->rel,
            'is_active'   => $this->isItemActive($item),
            'children'    => $children,
        ];
    }

    private function shouldDisplayItem(MenuItem $item): bool
    {
        $hideMissingRoutes = config('portal-ui.navigation.hide_missing_routes', true);  

        // 1. Oculta se a rota não existir no sistema
        if ($hideMissingRoutes && !empty($item->route) && !Route::has($item->route)) 
        {
            return false;
        }

        // 2. Valida Múltiplas Permissões / Perfis
        if (!empty($item->can_permission)) 
        {
            if (!Auth::check()) 
            {
                return false;
            }

            // Converte strings como "user,manager" ou "docente|aluno|servidor" em array ['user', 'manager']
            $permissions = array_filter(array_map('trim', preg_split('/[,|]/', $item->can_permission)));

            // Gate::any retorna TRUE se o usuário logado tiver PELO MENOS UMA das permissões
            if (!empty($permissions) && !Gate::any($permissions)) 
            {
                return false;
            }
        }

        // 3. Oculta itens exclusivos de visitantes para usuários logados
        if ($item->is_guest_only && Auth::check()) 
        {
            return false;
        }

        return true;
    }

    private function isItemActive(MenuItem $item): bool
    {
        if (!empty($item->active_pattern)) {
            return request()->routeIs($item->active_pattern) || request()->routeIs($item->active_pattern . '.*');
        }

        if (!empty($item->route)) {
            return request()->routeIs($item->route);
        }

        return false;
    }
}