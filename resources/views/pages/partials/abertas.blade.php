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
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $projeto->tituloProjeto }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                @foreach($projeto->cursos as $curso)     
                                    <span class="inline-flex items-center px-2 py-0.5 mb-1 mt-1 rounded text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">                                           
                                        {{ $curso->nomeCurso ?: $curso->codigoCurso }}
                                    </span>
                                @endforeach
                            </p>
                        </td>
                        <td class="px-4 py-3 px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $projeto->nomePessoaResponsavel ?? 'Não informado' }}</td>
                        <td class="px-4 py-3 px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
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
                                :href="route('inscricoes.create', $projeto)"
                            >
                                <i class="fa-solid fa-calendar-days"></i>
                            </x-portal::button>
                        </td>
                    </tr>
                @endforeach
            </x-slot:body>
        </x-portal::table>
    </x-portal::card>
        
    {{ $inscricoesAbertas->links() }}
@endif