@if(blank($projetos))
    <x-portal::alert variant="warning" title=" ">
        Nenhum projeto disponível no momento
    </x-portal::alert>    
@else
    <x-portal::card>
        <div class="grid grid-cols-4 gap-4 md:grid-cols-3">
            <!-- Ocupa 3 das 4 colunas -->
            <div class="md:col-span-2">
                <x-portal::input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Busque por Projeto, Curso, Docente ou Linha de Pesquisa"
                    wrapperClass="mb-0"
                />
            </div>

            <!-- Ocupa 1 coluna -->
            <div class="flex flex-col justify-end md:col-span-1">
                <x-portal::button 
                    variant="secondary" 
                    icon="fa-eraser" 
                    wire:click="cleanFilters"
                >
                    Limpar filtros
                </x-portal::button>
            </div>                
        </div>
    </x-portal::card>

    <x-portal::card padding="false">
        <x-portal::table>
            <x-slot:head>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Projeto</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Docente</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Curso(s)</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Linha de Pesquisa</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Aluno Externo a USP?</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400"></th>
                </tr>
            </x-slot:head>

            <x-slot:body>
                @foreach($projetos as $projeto)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="flex items-start gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $projeto->tituloProjeto }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Inscrições:                                         
                                        {{ $projeto->editais->first()?->dataInicioEdital ? \Carbon\Carbon::parse($projeto->editais->first()->dataInicioEdital)->format('d/m/Y') : '-' }} - 
                                        {{ $projeto->editais->first()?->dataTerminoEdital ? \Carbon\Carbon::parse($projeto->editais->first()->dataTerminoEdital)->format('d/m/Y') : '-' }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $projeto->nomePessoaResponsavel }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                            @forelse($projeto->cursos as $curso)
                                @php
                                    $temp = explode('|', $curso->codigoCurso);
                                    $nomeCurso = Uspdev\Replicado\Graduacao::nomeCurso($temp[0]);
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 mb-1 rounded text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                    {{ $nomeCurso ?: $curso->nomeCurso ?: $curso->codigoCurso }}
                                </span>
                            @empty
                                <span class="text-xs text-gray-400 italic">Não informada</span>
                            @endforelse
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $projeto->linhaPesquisaProjeto }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $projeto->statusExternoProjeto == 'S' ? 'Sim' : 'Não' }}</td>
                        <td class="px-4 py-3 text-right">
                            <x-portal::resource-actions
                                only="view"
                                mode="icon"
                                :viewHref="route('show', ['id' => $projeto->id])"
                                viewVariant="ghost"
                            />
                        </td>
                    </tr>
                @endforeach
            </x-slot:body>
        </x-portal::table>
    </x-portal::card>
@endif