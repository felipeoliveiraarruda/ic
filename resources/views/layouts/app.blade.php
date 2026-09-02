@extends('portal-ui::layouts.app')

@section('topbar-actions')
    @can('admin')
        @if(Route::has('senhaunica-users.index'))
            <a href="{{ route('senhaunica-users.index') }}"
                class="text-gray-500 hover:text-portal hover:bg-gray-50 p-2 rounded-lg transition-all"
                title="Usuários SenhaUnica">
                <i class="fa fa-users text-lg"></i>
            </a>
        @endif

        @if(Route::has('SenhaunicaLoginAsForm'))
            <a href="{{ route('SenhaunicaLoginAsForm') }}"
                class="text-gray-500 hover:text-portal hover:bg-gray-50 p-2 rounded-lg transition-all"
                title="Assumir Identidade (LoginAs)">
                <i class="fa fa-user-ninja text-lg"></i>
            </a>
        @endif

        @if(session()->has('senhaunica-socialite.undo_loginas') && Route::has('SenhaunicaUndoLoginAs'))
            <a href="{{ route('SenhaunicaUndoLoginAs') }}"
                class="text-red-600 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition-all"
                title="Desfazer Assumir Identidade (Voltar para o meu usuário)">
                <i class="fa fa-undo text-lg"></i>
            </a>
        @endif
    @endcan
@endsection

@section('user-menu')
    @if(session()->has('senhaunica-socialite.undo_loginas') && Route::has('SenhaunicaUndoLoginAs'))
        <a href="{{ route('SenhaunicaUndoLoginAs') }}"
           class="flex items-center gap-3 px-4 py-2 text-sm text-amber-700 hover:bg-amber-50">
            <i class="fa fa-rotate-left w-4"></i>
            <span>Desfazer LoginAs</span>
        </a>
    @endif

    @can('admin')
        @if(Route::has('senhaunica-users.index'))
            <a href="{{ route('senhaunica-users.index') }}"
               class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                <i class="fa fa-id-card w-4"></i>
                <span>Usuários SenhaUnica</span>
            </a>
        @endif

        @if(Route::has('SenhaunicaLoginAsForm'))
            <a href="{{ route('SenhaunicaLoginAsForm') }}"
               class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                <i class="fa fa-user-secret w-4"></i>
                <span>Assumir identidade</span>
            </a>
        @endif
    @endcan
@endsection