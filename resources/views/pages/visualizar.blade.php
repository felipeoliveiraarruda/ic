<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Illuminate\Validation\Rule;
use App\Models\Projeto;
use App\Models\Interesse;
use Carbon\Carbon;

new class extends Component
{
    public Projeto $projeto;

    // Propriedades do Modal de Interesse
    public bool $showInterestModal = false;
    public string $nomeInteresse = '';
    public string $emailInteresse = '';
    public bool $notificacoesInteresse = true;

    // Regras de Validação
    protected function rules(): array
    {
        return [
            'nomeInteresse'         => ['required', 'string', 'max:255'],
            'emailInteresse'        => ['required', 'email', 'max:255', Rule::unique('interesses', 'emailInteresse')->where('codigoProjeto', $this->projeto->id)],
            'notificacoesInteresse' => ['boolean'],
        ];
    }

    // Mensagens Personalizadas
    protected function messages(): array
    {
        return 
        [
            'emailInteresse.unique' => 'Você já cadastrou interesse neste projeto anteriormente.',
            'emailInteresse.required' => 'O e-mail é obrigatório.',
            'nomeInteresse.required'  => 'O nome é obrigatório.',
        ];
    }    

    public function mount($id)
    {
        // Carrega o projeto pelo ID passado na rota
        $this->projeto = Projeto::with(['editais', 'cursos'])->findOrFail($id);
    }

    public function openInterestModal(): void
    {
        $this->reset(['nomeInteresse', 'emailInteresse']);
        $this->notificacoesInteresse = true;
        $this->resetValidation();
        $this->showInterestModal = true;
    }

    public function closeInterestModal(): void
    {
        $this->showInterestModal = false;
    }

    public function salvarInteresse() : void
    {
        $this->validate();

        Interesse::create([
            'codigoProjeto'         => $this->projeto->id,
            'nomeInteresse'         => $this->nomeInteresse,
            'emailInteresse'        => $this->emailInteresse,
            'notificacoesInteresse' => ($this->notificacoesInteresse == true ? 'S' : 'N'),
        ]);

        session()->flash('success', 'Interesse registrado com sucesso! Entraremos em contato quando as inscrições abrirem.');
        $this->closeInterestModal();        
    }    

    #[Computed]
    public function inscricoesNaoIniciaram(): bool
    {
        $edital = $this->projeto->editais->first();

        if (!$edital || !$edital->dataInicioEdital) 
        {
            // Altere para false caso NÃO queira mostrar o botão quando não existir edital
            return true; 
        }

        // Retorna true se a data atual for menor que a data de início das inscrições
        return Carbon::today()->lt(Carbon::parse($edital->dataInicioEdital));
    }


    public function render()
    {
        return view('pages.visualizar');
    }
}
?>
<div>
    {{-- Banner de Cabeçalho --}}
    <div class="mb-6 rounded-2xl shadow-lg relative overflow-hidden bg-portal-gradient">
        <div class="relative z-10 p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                        <i class="fa fa-layer-group text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-1">
                            Banco de Ofertas - Iniciação Científica EEL/USP
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Conteúdo Principal --}}
    <div class="space-y-6">
        <x-portal::flash-messages />

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
            <x-portal::card class="mb-0">
                <x-slot:header>
                    <!-- Adicionado 'w-full' para ocupar toda a largura do card -->
                    <div class="flex w-full flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        
                        <!-- Lado Esquerdo: Ícone + Título e ID -->
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-portal-gradient text-white shadow-sm">
                                <i class="fa fa-tag text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <h2 class="text-base font-semibold text-gray-900 truncate">
                                    {{ $projeto->tituloProjeto }}
                                </h2>
                                <p class="text-xs text-gray-500">ID # {{ $projeto->id }}</p>
                            </div>
                        </div>

                        <!-- Lado Direito: Botões (forçados para a direita com 'sm:ms-auto') -->
                        <div class="flex items-center gap-2 flex-shrink-0 sm:ms-auto">
                            @if($this->inscricoesNaoIniciaram)
                                <x-portal::button wire:click="openInterestModal" icon="fa-solid fa-heart">
                                    Tenho Interesse
                                </x-portal::button>
                            @else
                                <x-portal::button wire:click="openInterestModal" icon="fa-solid fa-heart">
                                    Inscreva-se
                                </x-portal::button>
                            @endif

                            <x-portal::button :href="route('home')" variant="secondary" icon="fa-solid fa-arrow-rotate-left">
                                Voltar
                            </x-portal::button>
                        </div>

                        <!-- ========================================================= -->
                        <!-- MODAL DE CADASTRO DE INTERESSE                            -->
                        <!-- ========================================================= -->
                        @if($showInterestModal)
                        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-all"
                            x-data
                            x-on:keydown.escape.window="$wire.closeInterestModal()">

                            <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl max-w-md w-full p-6 border border-slate-100 dark:border-slate-800 transform transition-all animate-in fade-in zoom-in duration-200">
                                <!-- Cabeçalho -->
                                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-950 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                            <i class="fa-solid fa-bell text-lg"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                                                Demonstrar Interesse
                                            </h3>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                                {{ $projeto->tituloProjeto }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Formulário -->
                                <form wire:submit.prevent="salvarInteresse" class="space-y-4">
                                    <x-portal::input
                                        label="Nome Completo"
                                        wire:model.live="nomeInteresse"
                                        placeholder="Digite seu nome completo"
                                        required
                                    />

                                    <x-portal::input
                                        label="E-mail"
                                        wire:model.live="emailInteresse"
                                        placeholder="eu.email@exemplo.com"
                                        type="email"
                                        required
                                    />  

                                    <div class="flex items-start gap-3">
                                        <input type="checkbox" class="mt-1 rounded border-gray-300 text-portal focus:ring-portal/30" name="notificacoesInteresse">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 text-justify">Desejo receber e-mails e avisos quando abrir o período de inscrições para este projeto ou quando houver novos projetos semelhantes</p>
                                        </div>
                                    </div>                                    
                                    
                                    <!-- Botões de Ação -->
                                    <div class="flex items-center justify-end gap-3 mt-2">
                                        <x-portal::button type="button" variant="secondary" click="closeInterestModal">
                                            Cancelar
                                        </x-portal::button>

                                        <button wire:click="salvarInteresse" 
                                                type="button" 
                                                wire:loading.attr="disabled"
                                                class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-medium text-sm transition flex items-center gap-2 shadow-md shadow-red-500/20 disabled:opacity-50">
                                            <span wire:loading.remove wire:target="salvarInteresse" class="flex items-center gap-2">
                                                Confirmar
                                            </span>
                                            <span wire:loading wire:target="salvarInteresse" class="flex items-center gap-2">
                                                <i class="fa-solid fa-spinner fa-spin"></i> Registrando...
                                            </span>
                                        </button>
                                    </div>
                                </form>    
                            </div>
                        </div>
                        @endif

                    </div>
                </x-slot:header>

                <dl class="space-y-4">
                    <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                        <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Docente</dt>
                        <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->nomePessoaResponsavel }}</dd>
                    </div>

                    <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                        <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Curso</dt>
                        <dd class="md:col-span-2 text-sm text-gray-800">
                            @forelse($projeto->cursos as $curso)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                    {{ $curso->nomeCurso }}
                                </span>
                            @empty
                                <span class="text-xs text-gray-400 italic">Não informada</span>
                            @endforelse
                        </dd>
                    </div>                    
                    
                    <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                        <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Descrição</dt>
                        <dd class="md:col-span-2 text-sm text-gray-800 text-justify">{{ $projeto->descricaoProjeto }}</dd>
                    </div>

                    <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                        <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Período</dt>
                        <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->dataInicioProjeto->format('d/m/Y') }} - {{ $projeto->dataTerminoProjeto->format('d/m/Y') }}</dd>
                    </div>                    

                    <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                        <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Inscrições</dt>
                        <dd class="md:col-span-2 text-sm text-gray-800">
                            {{ $projeto->editais->first()?->dataInicioEdital ? \Carbon\Carbon::parse($projeto->editais->first()->dataInicioEdital)->format('d/m/Y') : '-' }} - 
                            {{ $projeto->editais->first()?->dataTerminoEdital ? \Carbon\Carbon::parse($projeto->editais->first()->dataTerminoEdital)->format('d/m/Y') : '-' }}                        
                        </dd>
                    </div>                         

                    <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                        <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Linha de Pesquisa</dt>
                        <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->linhaPesquisaProjeto }}</dd>
                    </div>

                    <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                        <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Informações</dt>
                        <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->informacoesProjeto }}</dd>
                    </div>

                    <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                        <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Pré-requisitos</dt>
                        <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->preRequisitosProjeto }}</dd>
                    </div>

                    <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                        <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Bolsa de Estudo</dt>
                        <dd class="md:col-span-2 text-sm text-gray-800">{{ ($projeto->tipoBolsaProjeto == 'Com Bolsa' ? $projeto->tipoBolsaProjeto.' - '.$projeto->bolsaProjeto : $projeto->tipoBolsaProjeto) }}</dd>
                    </div>

                    <div class="grid grid-cols-1 gap-2 border-b border-gray-100 pb-4 md:grid-cols-3">
                        <dt class="text-sm font-semibold uppercase tracking-wide text-gray-500">Aceita Alunos Externo a USP?</dt>
                        <dd class="md:col-span-2 text-sm text-gray-800">{{ $projeto->statusExternoProjeto == 'S' ? 'Sim' : 'Não' }}</dd>
                    </div>                     
                </dl>     
            </x-portal::card>
        </div>
    </div>
</div>