<?php

namespace App\Providers;

use App\Services\NavigationService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function boot(NavigationService $navigationService): void
    {
        // Executa exatamente no momento em que a view vai ser desenhada
        View::composer('*', function ($view) use ($navigationService) 
        {
            $menu = $navigationService->getMenu();

            // Preenche a configuração em memória que o pacote portal-ui consome
            config(['portal-ui.navigation.groups' => $menu]);

            // Injeta a variável $groups na view
            $view->with('groups', $menu);
        });
    }
}