<!-- CONTEÚDO DA ABA 3: PRÓXIMAS INSCRIÇÕES -->
@if($proximasInscricoes->isEmpty())
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-12 text-center text-slate-500 dark:text-slate-400">
            <i class="fa-solid fa-clock-rotate-left text-4xl mb-3 opacity-40"></i>
            <p class="text-base font-medium">Nenhum edital futuro cadastrado no momento.</p>
        </div>
    </div>
@else
    <x-portal::card padding="false">
        <x-portal::table>
            <x-slot:head>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Projeto</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Docente Responsável</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Abertura Prevista</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400"></th>
                </tr>
            </x-slot:head>        
            <x-slot:body>
                @foreach($proximasInscricoes as $projeto)
                    @php $edital = $projeto->editais->first(); @endphp
                    <tr>
                        <td class="px-4 py-3">
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $projeto->tituloProjeto }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                <span class="inline-flex items-center px-2 py-0.5 mb-1 mt-1 rounded text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">                                           
                                    Linha: {{ $projeto->linhaPesquisaProjeto ?? 'Não especificada' }}
                                </span>
                            </p>
                        </td>
                        <td class="px-4 py-3 px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $projeto->nomePessoaResponsavel ?? 'Não informado' }}</td>
                        <td class="px-4 py-3 px-4 py-3 text-sm text-gray-500 dark:text-gray-400">Abre em: {{ \Carbon\Carbon::parse($edital->dataInicioEdital)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <x-portal::resource-actions
                                :only="'view'"
                                mode="icon"
                                :viewHref="route('admin.projetos.show', $projeto)"
                                viewVariant="ghost"
                            />
                        </td>
                    </tr>
                @endforeach
            </x-slot:body>
        </x-portal::table>
    </x-portal::card>
    {{ $proximasInscricoes->links() }}
@endif
