@if($minhasInscricoes->isEmpty())
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-12 text-center text-slate-500 dark:text-slate-400">
            <i class="fa-solid fa-folder-open text-4xl mb-3 opacity-40"></i>
            <p class="text-base font-medium">Você ainda não se inscreveu em nenhum projeto.</p>
            <p class="text-xs mt-1">Navegue pela aba <strong>Inscrições Abertas</strong> para encontrar vagas disponíveis.</p>
        </div>
    </div>
@else
<x-portal::card padding="false">
    <x-portal::table>
        <x-slot:head>
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Projeto / Responsável</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Data da Inscrição</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400"></th>
            </tr>
        </x-slot:head>
        <x-slot:body>
            @foreach($minhasInscricoes as $inscricao)
                @php $projeto = $inscricao->editais->projeto->first(); @endphp
                <tr>
                    <td class="px-4 py-3">
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $projeto->tituloProjeto }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            <span class="inline-flex items-center px-2 py-0.5 mb-1 mt-1 rounded text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">                                           
                                Docente: {{ $projeto->nomePessoaResponsavel ?? 'Não informado' }}
                            </span>
                        </p>
                    </td>
                    <td class="px-4 py-3 px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $inscricao->created_at->format('d/m/Y \à\s H:i') }}</td>
                    <td class="px-4 py-3 px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                        @if($inscricao->statusInscricao === 'A')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-300">
                                <i class="fa-solid fa-circle-check"></i> Selecionado
                            </span>
                        @elseif($inscricao->status === 'R')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 border border-rose-300">
                                <i class="fa-solid fa-circle-xmark"></i> Não Selecionado
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border border-amber-300">
                                <i class="fa-solid fa-clock"></i> Em Análise
                            </span>
                        @endif                                        
                    </td>
                    <td class="px-4 py-3 text-right">
                        <x-portal::resource-actions
                            :only="'view'"
                            mode="icon"
                            :viewHref="route('admin.projetos.show', $projeto)"
                            viewVariant="ghost"
                        />
                    </td>
                @endforeach
            </x-slot:body>
        </x-portal::table>
    </x-portal::card>
    {{ $minhasInscricoes->links() }}
@endif