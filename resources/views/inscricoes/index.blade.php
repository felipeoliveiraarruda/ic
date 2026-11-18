<?php

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Projeto;
use App\Models\Inscricao;

new class extends Component
{
    use WithPagination;

    public string $abaAtiva = 'minhas'; // Aba inicial: 'minhas', 'abertas', 'proximas'

    public function alterarAba(string $aba): void
    {
        $this->abaAtiva = $aba;
        $this->resetPage();
    }

    public function render()
    {
        $hoje = Carbon::today()->format('Y-m-d');
        $userId = Auth::id();

        // 1. Inscrições em que o usuário logado JÁ está participando
        $minhasInscricoes = Inscricao::with(['editais', 'editais.projeto'])
            ->where('codigoUsuario', $userId)
            ->latest()
            ->paginate(10, ['*'], 'minhas_page');

        // IDs dos projetos nos quais o usuário já se inscreveu
        $projetosInscritosIds = Inscricao::where('codigoUsuario', $userId)->pluck('codigoEdital')->toArray();

        // 2. Projetos com Inscrições Abertas (hoje entre dataInicioEdital e dataTerminoEdital)
        $inscricoesAbertas = Projeto::with(['editais', 'cursos'])
            ->whereHas('editais', function ($q) use ($hoje) {
                $q->where('dataInicioEdital', '<=', $hoje)
                  ->where('dataTerminoEdital', '>=', $hoje);
            })
            ->whereNotIn('id', $projetosInscritosIds) // Oculta projetos em que já está inscrito
            ->latest()
            ->paginate(10, ['*'], 'abertas_page');

        // 3. Próximas Inscrições (hoje anterior a dataInicioEdital)
        $proximasInscricoes = Projeto::with(['editais', 'cursos'])
            ->whereHas('editais', function ($q) use ($hoje) {
                $q->where('dataInicioEdital', '>', $hoje);
            })
            ->whereNotIn('id', $projetosInscritosIds)
            ->orderBy(
                // Ordena pelos editais que abrirão mais cedo
                \App\Models\Edital::select('dataInicioEdital')
                    ->whereColumn('editais.codigoProjeto', 'projetos.id')
                    ->limit(1),
                'asc'
            )
            ->paginate(10, ['*'], 'proximas_page');

        return view('inscricoes.index', [
            'minhasInscricoes'   => $minhasInscricoes,
            'inscricoesAbertas'  => $inscricoesAbertas,
            'proximasInscricoes' => $proximasInscricoes,
        ]);
    }
};
?>

@section('breadcrumbs')
    <a href="{{ Route::has('inscricoes.index') ? route('inscricoes.index') : '#' }}" class="hover:text-gray-700 hover:underline">Dashboard</a>
@endsection

<div class="space-y-6">
    <div class="relative z-10 p-6 mb-6 rounded-2xl shadow-lg relative overflow-hidden bg-portal-gradient">
        <div class="flex items-center gap-4 text-white">
            <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/10">
                <i class="fa-solid fa-graduation-cap text-2xl text-amber-300"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold">Dashboard</h1>
                <p class="text-xs text-indigo-200 mt-1">Acompanhe suas candidaturas e descubra novas oportunidades de pesquisa.</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <x-portal::card>
            <x-slot:header>
                <div class="flex items-center justify-between gap-3">
                    <span class="font-semibold text-gray-900 dark:text-gray-100">Minhas inscrições</span>
                </div>
            </x-slot:header>

            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $minhasInscricoes->total() }}</p>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Inscrições que estou participando</p>
        </x-portal::card>

        <x-portal::card>
            <x-slot:header>
                <div class="flex items-center justify-between gap-3">
                    <span class="font-semibold text-gray-900 dark:text-gray-100">Inscrições abertas</span>
                </div>
            </x-slot:header>

            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $inscricoesAbertas->total() }}</p>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Projetos com período de inscrições abertas</p>
        </x-portal::card>

        <x-portal::card>
            <x-slot:header>
                <div class="flex items-center justify-between gap-3">
                    <span class="font-semibold text-gray-900 dark:text-gray-100">Projetos</span>
                </div>
            </x-slot:header>

            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $proximasInscricoes->total() }}</p>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Projetos com período de inscrições em andamento</p>
        </x-portal::card>
    </div>

    <!-- Navegação por Abas -->
    <x-portal::card x-data="{ activeTab: 'geral' }">
        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between mb-4">
            <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap">
                <x-portal::button                            
                    :variant="$abaAtiva === 'minhas' ? 'primary' : 'outline'"
                    size="sm"
                    click="alterarAba('minhas')"
                >
                    <i class="fa-solid fa-user-check"></i>
                    <span>Minhas inscrições</span>
                </x-portal::button>

                <x-portal::button                            
                    :variant="$abaAtiva === 'abertas' ? 'primary' : 'outline'"
                    size="sm"
                    click="alterarAba('abertas')"                    
                >
                    <i class="fa-solid fa-door-open"></i>
                    <span>Inscrições Abertas</span>
                </x-portal::button>
                
                <x-portal::button
                    :variant="'outline'"
                    size="sm"
                    :variant="$abaAtiva === 'proximas' ? 'primary' : 'outline'"
                    size="sm"
                    click="alterarAba('proximas')"
                >
                    <i class="fa-solid fa-calendar-days"></i>
                    <span>Próximas Inscrições</span>
                </x-portal::button>
            </div>
        </div>

        <!-- CONTEÚDO DA ABA 1: MINHAS INSCRIÇÕES -->
        @if($abaAtiva === 'minhas')            
            @include('inscricoes.partials.minhas')
        @endif

        <!-- CONTEÚDO DA ABA 2: INSCRIÇÕES ABERTAS -->
        @if($abaAtiva === 'abertas')            
            @include('inscricoes.partials.abertas')
        @endif 
        
        <!-- CONTEÚDO DA ABA 3: PRÓXIMAS INSCRIÇÕES -->
        @if($abaAtiva === 'proximas')
            @include('inscricoes.partials.proximas')
        @endif        
    </x-portal::card>
</div>