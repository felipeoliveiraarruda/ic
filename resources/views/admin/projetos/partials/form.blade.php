<!-- Card 1: Informações Gerais -->
<x-portal::card>
    <x-slot:header>
        <div class="flex w-full flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3 min-w-0 flex-1">
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-portal-gradient text-white shadow-sm">
                    <i class="fa fa-info-circle text-sm"></i>
                </div>
                <div class="min-w-0">
                    <h2 class="text-base font-semibold text-gray-900 truncate">Informações Gerais</h2>
                    <p class="text-xs text-gray-500">Dados fundamentais e docente responsável pelo projeto</p>
                </div>
            </div>

            <!-- Botões alinhados à direita -->
            <div class="flex items-center gap-2 flex-shrink-0 sm:ms-auto">
               <x-portal::button type="submit" icon="fa-solid fa-floppy-disk" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">Salvar</span>
                    <span wire:loading wire:target="save">Salvando...</span>
                </x-portal::button>

                <x-portal::button :href="route('admin.projetos')" variant="secondary" icon="fa-solid fa-arrow-rotate-left">
                    Cancelar
                </x-portal::button>
            </div>
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
        <!-- Título do Projeto -->
        <div class="md:col-span-2">
            <x-portal::input
                label="Título do Projeto"
                wire:model.live="tituloProjeto"
                placeholder="Digite o título do projeto..."
                :error="$errors->first('tituloProjeto')"
                required 
            />
        </div>

        <!-- Docente Responsável -->
        <div>
            @if(!empty($this->docentesOptions))
                <x-portal::select
                    label="Docente Responsável"
                    wire:model.live="codigoPessoaResponsavel"
                    :options="['' => 'Selecione o docente...'] + $this->docentesOptions"
                    :error="$errors->first('codigoPessoaResponsavel')"
                    required
                />
            @else
                <x-portal::input
                    label="Nº USP do Docente Responsável *"
                    wire:model.live="codigoPessoaResponsavel"
                    placeholder="Digite o Nº USP do docente"
                    :error="$errors->first('codigoPessoaResponsavel')"
                    required
                />
            @endif
        </div>

        <!-- Aceita Aluno Externo -->
        <div>
            <x-portal::select
                label="Aceita Aluno Externo à USP?"
                wire:model.live="statusExternoProjeto"
                :options="['N' => 'Não', 'S' => 'Sim']"
                required
            />
        </div>

        <!-- Linha de Pesquisa -->
        <div class="md:col-span-2">
            <x-portal::input
                label="Linha de Pesquisa"
                wire:model.live="linhaPesquisaProjeto"
                placeholder="Ex: Inteligência Artificial, Bioquímica Aplicada, Materiais..."
                required
            />
        </div>
    </div>
</x-portal::card>

<!-- Card 2: Detalhes, Vagas e Bolsas -->
<x-portal::card>
    <x-slot:header>
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-portal-gradient text-white shadow-sm">
                <i class="fa fa-graduation-cap text-sm"></i>
            </div>
            <div>
                <h2 class="text-base font-semibold text-gray-900">Vagas, Bolsas e Requisitos</h2>
                <p class="text-xs text-gray-500">Especifique as condições da oportunidade</p>
            </div>
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        <!-- Nº de Vagas -->
        <div>
            <x-portal::input
                type="number"
                min="1"
                label="Número de Vagas"
                wire:model.live="numeroVagaProjeto"
                :error="$errors->first('numeroVagaProjeto')"
                required
            />
        </div>

        <!-- Tipo de Bolsa -->
        <div>
            <x-portal::select
                label="Tipo de Bolsa"
                wire:model.live="tipoBolsaProjeto"
                :options="['' => 'Selecione...', 'Com Bolsa' => 'Com Bolsa', 'Sem Bolsa' => 'Sem Bolsa', 'Possível Bolsa' => 'Possível Bolsa']"
                :error="$errors->first('tipoBolsaProjeto')"
                required                        
            />
        </div>

        <!-- Nome/Especificação da Bolsa (Condicional) -->
        <div>
            <x-portal::input
                label="Bolsa / Financiador {{ $tipoBolsaProjeto === 'Com Bolsa' ? '*' : '(Opcional)' }}"
                wire:model.live="bolsaProjeto"
                placeholder="Ex: FAPESP, CNPq, PUB..."
                :disabled="$tipoBolsaProjeto !== 'Com Bolsa'"
                :error="$errors->first('bolsaProjeto')"
            />
        </div>

        <!-- Descrição do Projeto -->
        <div class="md:col-span-3">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Descrição do Projeto
            </label>
            <textarea
                wire:model.live="descricaoProjeto"
                rows="3"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                placeholder="Descreva os objetivos e o resumo da pesquisa..."
                :error="$errors->first('descricaoProjeto')"
                required
            ></textarea>
            @error('descricaoProjeto') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Pré-requisitos -->
        <div class="md:col-span-3">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Pré-requisitos
            </label>
            <textarea
                wire:model.live="preRequisitosProjeto"
                rows="3"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                placeholder="Ex: Ter cursado a disciplina X, disponibilidade de 10h semanais..."
                :error="$errors->first('preRequisitosProjeto')"
                required
            ></textarea>
            @error('preRequisitosProjeto') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Informações Adicionais -->
        <div class="md:col-span-3">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Informações Adicionais
            </label>
            <textarea
                wire:model.live="informacoesProjeto"
                rows="3"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                placeholder="Qualquer outra orientação complementar..."
                required
            ></textarea>
            @error('informacoesProjeto') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>
    </div>
</x-portal::card>

<!-- Card 3: Prazos e Período de Inscrição -->
<x-portal::card>
    <x-slot:header>
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-portal-gradient text-white shadow-sm">
                <i class="fa fa-calendar-alt text-sm"></i>
            </div>
            <div>
                <h2 class="text-base font-semibold text-gray-900">Prazos da Vigência e Inscrições</h2>
                <p class="text-xs text-gray-500">Defina as datas da vigência da pesquisa e do período de edital</p>
            </div>
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <!-- Prazos do Projeto -->
        <div class="space-y-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700">
            <h3 class="font-medium text-sm text-gray-900 dark:text-gray-100 flex items-center gap-2">
                <i class="fa fa-clock text-indigo-500"></i> Período de Execução do Projeto
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-portal::input 
                        type="date" 
                        label="Início do Projeto" 
                        wire:model.live="dataInicioProjeto" 
                        :error="$errors->first('dataInicioProjeto')"
                        required
                    />
                </div>
                <div>
                    <x-portal::input 
                        type="date" 
                        label="Término do Projeto" 
                        wire:model.live="dataTerminoProjeto" 
                        :error="$errors->first('dataTerminoProjeto')"
                        required 
                    />
                </div>
            </div>
        </div>

        <!-- Prazos de Inscrição (Edital) -->
        <div class="space-y-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700">
            <h3 class="font-medium text-sm text-gray-900 dark:text-gray-100 flex items-center gap-2">
                <i class="fa fa-user-plus text-emerald-500"></i> Período de Inscrição de Alunos
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-portal::input 
                        type="date" 
                        label="Início das Inscrições" 
                        wire:model.live="dataInicioInscricao"
                        :error="$errors->first('dataInicioInscricao')"
                        required 
                    />
                </div>
                <div>
                    <x-portal::input 
                        type="date" 
                        label="Término das Inscrições" 
                        wire:model.live="dataTerminoInscricao" 
                        :error="$errors->first('dataTerminoInscricao')"
                        required 
                    />
                </div>
            </div>
        </div>
    </div>
</x-portal::card>

<!-- Card 4: Cursos Elegíveis -->
<x-portal::card>
    <x-slot:header>
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-portal-gradient text-white shadow-sm">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Cursos Elegíveis</h3>
                <p class="text-xs text-gray-500">Selecione quais cursos de graduação podem se candidatar:</p>
                @error('codigoCurso') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
            </div>            
        </div>
    </x-slot:header>

    @if(empty($cursosDisponiveis))
        <x-portal::alert variant="warning">
            Nenhum curso disponível encontrado.
        </x-portal::alert>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            @foreach($cursosDisponiveis as $codigo => $nome)
                @php
                    $temp = explode('|', $codigo)
                @endphp

                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer transition">
                    <input
                        type="checkbox"
                        value="{{ $codigo }}"
                        wire:model.live="codigoCurso"
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4"
                    />
                    <div class="text-xs">
                        <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $nome }}</p>
                        <p class="text-gray-400">Cód: {{ $temp[0] }}</p>
                    </div>
                </label>             
            @endforeach

            <!-- Campo dinâmico para Outro(s) -->
            @if(collect($codigoCurso)->contains(fn($cod) => str_contains((string) $cod, '88999')))
                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer transition">
                    <x-portal::input
                        label="Especifique o(s) outro(s) curso(s)"
                        wire:model.live="outroCursoText"
                        placeholder="Ex: Engenharia Nuclear, Licenciatura..."
                        required
                    />          
                </label>
            @else
                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer transition">
                  
                </label>
            @endif
        </div>         
    @endif
</x-portal::card>