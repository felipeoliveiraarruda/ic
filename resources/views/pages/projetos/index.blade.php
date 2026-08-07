<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Projetos;

new class extends Component
{
    public function render()
    {
        return view('pages.projetos.index',
        [
            'projetos'  => (session('level') == 'admin' ?  Projetos::paginate(5) : Projetos::all()),
            'level'     => session('level'),
        ]);
    }
};
?>

@section('breadcrumbs')
    <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : '#' }}" class="hover:text-gray-700 hover:underline">Dashboard</a>
    <span>/</span>
    <span>Projetos</span>
@endsection

<div class="space-y-6">
    <x-portal::page-header
        title="Projetos"
        subtitle="Visão operacional dos projetos cadastrados.">

        @if($level == 'admin')
        <x-slot:actions>
            <x-portal::button icon="fa-plus" :href="route('admin.projetos.create')">Novo Projeto</x-portal::button>
        </x-slot:actions>
        @endif

    </x-portal::page-header>

    <x-portal::card padding="false">
        <x-portal::table>
            <x-slot:head>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Projeto</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Curso</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Ações</th>
                </tr>
            </x-slot:head>

            <x-slot:body>
                @foreach($projetos as $projeto)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40">
                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">{{ $projeto->tituloProjeto }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $projeto->codigoCurso }}</td>
                        <td class="px-4 py-3 text-right">                            
                            @if($level == 'admin')
                            <x-portal::resource-actions
                                :viewHref="route('admin.projetos.show', ['codigoProjeto' => $projeto->codigoProjeto])"
                                :edit-href="'#'"
                                :delete-onclick="'window.alert(&quot;Excluir item de demonstração&quot;)'"
                            />
                            @else
                            <x-portal::resource-actions
                                :viewHref="route('admin.projetos.show', ['codigoProjeto' => $projeto->codigoProjeto])"
                            />
                            @endif
                        </td>
                    </tr>
                @endforeach
            </x-slot:body>
        </x-portal::table>
    </x-portal::card>
</div>