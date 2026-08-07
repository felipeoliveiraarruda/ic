@extends('portal-ui::layouts.app')

@section('title', 'Home')

@section('content')
<div class="mb-6 rounded-2xl shadow-lg relative overflow-hidden bg-portal-gradient" zn_id="12">
    <div class="relative z-10 p-6" zn_id="6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4" zn_id="7">
            <div class="flex items-center gap-4" zn_id="42">
                <div class="w-14 h-14 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm" zn_id="20">
                    <i class="fa fa-layer-group text-white text-2xl" zn_id="21"></i>
                </div>
                <div zn_id="75">
                    <h1 class="text-2xl font-bold text-white mb-1" zn_id="8">
                       Banco de Ofertas - Iniciação Científica EEL/USP
                    </h1>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="space-y-6">
    <x-portal::card padding="false">
        <x-portal::table>
            <x-slot:head>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Projeto</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Curso</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Período</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Ações</th>
                </tr>
            </x-slot:head>

            <x-slot:body>
                @foreach($projetos as $projeto)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40">
                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">{{ $projeto->tituloProjeto }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $projeto->codigoCurso }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $projeto->periodoProjeto }}</td>
                        <td class="px-4 py-3 text-right">
                            <x-portal::resource-actions
                                :viewHref="route('show', ['codigoProjeto' => $projeto->codigoProjeto])"
                                mode="label"
                            />
                        </td>
                    </tr>
                @endforeach
            </x-slot:body>
        </x-portal::table>
    </x-portal::card>
</div>
@endsection