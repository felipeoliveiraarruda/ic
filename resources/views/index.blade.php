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

    <!-- Elementos decorativos no fundo (depois do conteúdo) -->
    <div class="portal-circle-decoration -top-1/2 -right-1/4 w-72 h-72"></div>
    <div class="portal-circle-decoration -bottom-1/2 -left-1/4 w-48 h-48"></div>
</div>
@endsection