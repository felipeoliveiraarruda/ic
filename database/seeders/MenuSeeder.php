<?php

namespace Database\Seeders;

use App\Models\MenuGroup;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $groups = config('portal-ui.navigation.groups', []);

        $groupOrder = 1;
        foreach ($groups as $groupKey => $groupData) {
            $group = MenuGroup::updateOrCreate(
                ['key' => $groupKey],
                [
                    'label' => $groupData['label'],
                    'order' => $groupOrder++,
                    'is_active' => true,
                    'codigoUsuarioCriacao'   => 1,
                    'codigoUsuarioAlteracao' => 1, 
                ]
            );

            $itemOrder = 1;
            foreach ($groupData['items'] ?? [] as $itemData) {
                MenuItem::updateOrCreate(
                    [
                        'menu_group_id' => $group->id,
                        'label' => $itemData['label'],
                    ],
                    [
                        'route' => $itemData['route'] ?? null,
                        'url' => $itemData['url'] ?? null,
                        'icon' => $itemData['icon'] ?? null,
                        'active_pattern' => $itemData['active'] ?? null,
                        'can_permission' => $itemData['can'] ?? null,
                        'is_guest_only' => $itemData['guest'] ?? false,
                        'order' => $itemOrder++,
                        'is_active' => true,
                        'codigoUsuarioCriacao'   => 1,
                        'codigoUsuarioAlteracao' => 1,                        
                    ]
                );
            }
        }
    }
}