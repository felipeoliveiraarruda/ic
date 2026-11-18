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
        $minhasInscricoes = Inscricao::with(['projeto.editais', 'projeto.cursos'])
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
                    <span class="font-semibold text-gray-900 dark:text-gray-100">Projetos em andamento</span>
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
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                @if($minhasInscricoes->isEmpty())
                    <div class="p-12 text-center text-slate-500 dark:text-slate-400">
                        <i class="fa-solid fa-folder-open text-4xl mb-3 opacity-40"></i>
                        <p class="text-base font-medium">Você ainda não se inscreveu em nenhum projeto.</p>
                        <p class="text-xs mt-1">Navegue pela aba <strong>Inscrições Abertas</strong> para encontrar vagas disponíveis.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wider">
                                <tr>
                                    <th class="px-5 py-3.5">Projeto / Responsável</th>
                                    <th class="px-5 py-3.5">Data da Inscrição</th>
                                    <th class="px-5 py-3.5 text-center">Status</th>
                                    <th class="px-5 py-3.5 text-right">Ação</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($minhasInscricoes as $inscricao)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                                        <td class="px-5 py-4">
                                            <div class="font-semibold text-slate-900 dark:text-white">
                                                {{ $inscricao->projeto->tituloProjeto }}
                                            </div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                                Docente: {{ $inscricao->projeto->nomePessoaResponsavel ?? 'Não informado' }}
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-xs text-slate-600 dark:text-slate-300">
                                            {{ $inscricao->created_at->format('d/m/Y \à\s H:i') }}
                                        </td>
                                        <td class="px-5 py-4 text-center">
                                            @if($inscricao->status === 'aprovado')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-300">
                                                    <i class="fa-solid fa-circle-check"></i> Selecionado
                                                </span>
                                            @elseif($inscricao->status === 'rejeitado')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 border border-rose-300">
                                                    <i class="fa-solid fa-circle-xmark"></i> Não Selecionado
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border border-amber-300">
                                                    <i class="fa-solid fa-clock"></i> Em Análise
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            <a href="{{ route('admin.projetos.show', $inscricao->projeto_id) }}" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-slate-300 dark:border-slate-700 text-xs font-medium rounded-lg text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                                <i class="fa-solid fa-eye"></i> Detalhes
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                        {{ $minhasInscricoes->links() }}
                    </div>
                @endif
            </div>
        @endif

        <!-- CONTEÚDO DA ABA 2: INSCRIÇÕES ABERTAS -->
        @if($abaAtiva === 'abertas')            
            @if($inscricoesAbertas->isEmpty())
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-12 text-center text-slate-500 dark:text-slate-400">
                    <i class="fa-solid fa-calendar-xmark text-4xl mb-3 opacity-40"></i>
                    <p class="text-base font-medium">Não há inscrições abertas no momento.</p>
                </div>
            </div>                    
            @else
                <x-portal::card padding="false">
                    <x-portal::table>
                        <x-slot:head>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Projeto</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Docente Responsável</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Período de Inscrições</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400"></th>
                            </tr>
                        </x-slot:head>

                        <x-slot:body>
                            @foreach($inscricoesAbertas as $projeto)
                                @php $edital = $projeto->editais->first(); @endphp
                                <tr>
                                    <td class="px-4 py-3">
                                        {{ $projeto->tituloProjeto }}
                                        
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach($projeto->cursos as $curso)
                                                <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                                                    {{ $curso->nomeCurso ?: $curso->codigoCurso }}
                                                </span>
                                            @endforeach
                                        </div></td>
                                    <td class="px-4 py-3">{{ $projeto->nomePessoaResponsavel ?? 'Não informado' }}</td>
                                    <td class="px-4 py-3">
                                        <i class="fa-regular fa-calendar-check mr-1"></i>
                                        {{ \Carbon\Carbon::parse($edital->dataInicioEdital)->format('d/m/Y') }} até {{ \Carbon\Carbon::parse($edital->dataTerminoEdital)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <x-portal::resource-actions
                                            :only="'view'"
                                            mode="icon"
                                            :viewHref="route('admin.projetos.show', $projeto)"
                                            viewVariant="ghost"
                                        />

                                        <x-portal::button
                                            :variant="'outline'"
                                            size="sm"
                                            title="Inscreva-se"
                                        >
                                            <i class="fa-solid fa-calendar-days"></i>
                                        </x-portal::button>
                                    </td>
                                </tr>
                            @endforeach
                        </x-slot:body>
                    </x-portal::table>
                </x-portal::card>
                    
                <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $inscricoesAbertas->links() }}
                </div>
            @endif
        @endif 
        
        <!-- CONTEÚDO DA ABA 3: PRÓXIMAS INSCRIÇÕES -->
        @if($abaAtiva === 'proximas')
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                @if($proximasInscricoes->isEmpty())
                    <div class="p-12 text-center text-slate-500 dark:text-slate-400">
                        <i class="fa-solid fa-clock-rotate-left text-4xl mb-3 opacity-40"></i>
                        <p class="text-base font-medium">Nenhum edital futuro cadastrado no momento.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wider">
                                <tr>
                                    <th class="px-5 py-3.5">Projeto</th>
                                    <th class="px-5 py-3.5">Docente Responsável</th>
                                    <th class="px-5 py-3.5">Abertura Prevista</th>
                                    <th class="px-5 py-3.5 text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($proximasInscricoes as $projeto)
                                    @php $edital = $projeto->editais->first(); @endphp
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                                        <td class="px-5 py-4">
                                            <div class="font-semibold text-slate-900 dark:text-white">
                                                {{ $projeto->tituloProjeto }}
                                            </div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                                Linha: {{ $projeto->linhaPesquisaProjeto ?? 'Não especificada' }}
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-xs text-slate-600 dark:text-slate-300">
                                            {{ $projeto->nomePessoaResponsavel ?? 'Não informado' }}
                                        </td>
                                        <td class="px-5 py-4 text-xs text-amber-600 dark:text-amber-400 font-medium">
                                            <i class="fa-regular fa-clock mr-1"></i>
                                            Abre em: {{ \Carbon\Carbon::parse($edital->dataInicioEdital)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-5 py-4 text-right flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.projetos.show', $projeto->id) }}" 
                                            class="px-3 py-1.5 border border-slate-300 dark:border-slate-700 text-xs font-medium rounded-lg text-slate-700 dark:text-slate-200 hover:bg-slate-100 transition">
                                                Detalhes
                                            </a>

                                            <button wire:click="openInterestModal" type="button" 
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-lg shadow-sm transition">
                                                <i class="fa-solid fa-heart"></i> Tenho Interesse
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                        {{ $proximasInscricoes->links() }}
                    </div>
                @endif
            </div>
        @endif        
    </x-portal::card>
</div>