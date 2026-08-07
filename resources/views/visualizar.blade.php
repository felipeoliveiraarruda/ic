@extends('portal-ui::layouts.app')

@section('title', 'Visualizar')

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
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <x-portal::card class="mb-0">
            <x-slot:header>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-portal-gradient text-white shadow-sm">
                            <i class="fa fa-tag text-sm"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-gray-900">Projeto</h2>
                            <p class="text-xs text-gray-500">ID # {{ $projeto->codigoProjeto }}</p>
                        </div>
                    </div>
                </div>
            </x-slot:header>

            <dl class="space-y-4">
                <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                    <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Título</dt>
                    <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->tituloProjeto }}</dd>
                </div>

                <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                    <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Curso</dt>
                    <dd class="md:col-span-2 text-sm text-gray-800">
                        <code class="rounded-lg bg-gray-100 px-2 py-1 text-xs">{{ $projeto->codigoCurso ?? '-' }}</code>
                    </dd>
                </div>

                <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                    <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Número de Horas</dt>
                    <dd class="md:col-span-2 text-sm text-gray-800">
                        <code class="rounded-lg bg-gray-100 px-2 py-1 text-xs">{{ $projeto->numeroHorasProjeto ?? '-' }}</code>
                    </dd>
                </div>

                <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                    <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Período</dt>
                    <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->periodoProjeto }}</dd>
                </div>

                @auth
                <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                    <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Descrição</dt>
                    <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->descricaoProjeto }}</dd>
                </div>

                <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                    <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Informações</dt>
                    <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->informacoesProjeto }}</dd>
                </div>
                @endauth
            </dl>     
        </x-portal::card> 

        <x-portal::button full="true" :href="route('admin.projetos.show', ['codigoProjeto' => $projeto->codigoProjeto])" icon="fa-solid fa-check">Tenho Interesse</x-portal::button>
        <x-portal::button full="true" :href="route('home')" variant="secondary" icon="fa-solid fa-arrow-rotate-left">Voltar</x-portal::button>
    </div>
</div>
@endsection