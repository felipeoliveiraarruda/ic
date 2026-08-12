<?php

use Livewire\Component;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Arr;
use App\Models\User;
use App\Models\Utils;
use App\Models\Projeto;

new class extends Component
{
    // Propriedades reativas sincronizadas com os inputs da view e com a URL
    #[Url]
    public string $search = '';

    #[Url]
    public string $filterDocente = '';

    #[Url]
    public string $filterPeriodo = '';

    #[Url]
    public string $filterExterno = '';

    public $docentes = [];

    public function mount()
    {
        if (Auth::check()) 
        {
            Utils::setSession(Auth::user()->id);

            $level = session('level');
            $vinculos = session('vinculos', []);

            if ($level === 'admin' || Arr::exists($vinculos, 'Docente')) 
            {
                return $this->redirectRoute('admin.dashboard');
            }

            if ($level === 'user') 
            {
                if (session()->has('url.intended')) 
                {
                    return redirect()->intended();
                }
                
                return $this->redirectRoute('admin.projetos');
            }

            return $this->redirect('/dashboard');
        }
    }

    // Limpa todos os filtros ativos
    public function cleanFilters(): void
    {
        $this->reset(['search', 'filterDocente', 'filterPeriodo', 'filterExterno']);
    }

    public function render()
    {
        $projetos = Projeto::query()
            // Filtro de texto genérico (Busca em Título, Curso, Linha de Pesquisa e Cód. Docente)
            ->when($this->search, function ($query) 
            {
                $searchTerm = '%' . $this->search . '%';
                
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('tituloProjeto', 'like', $searchTerm)
                      ->orWhere('codigoCurso', 'like', $searchTerm)
                      ->orWhere('linhaPesquisaProjeto', 'like', $searchTerm);
                });
            })
            // Filtro por Docente
            ->when($this->filterDocente !== '', function ($query) {
                $query->where('codigoPessoa', $this->filterDocente);
            })
            // Filtro por Período
            ->when($this->filterPeriodo !== '', function ($query) {
                $query->where('periodoProjeto', $this->filterPeriodo);
            })
            // Filtro por Aceite de Aluno Externo (S/N)
            ->when($this->filterExterno !== '', function ($query) {
                $query->where('statusExternoProjeto', $this->filterExterno);
            })
            ->get();

        $temps = Projeto::select('codigoPessoa')->whereNotNull('codigoPessoa')->groupBy('codigoPessoa')->get();

        foreach($temps as $temp)
        {
            $docente = Uspdev\Replicado\Pessoa::obterNome($temp->codigoPessoa);
            $this->docentes[$temp->codigoPessoa] = $docente;            
        }

        asort($this->docentes, SORT_LOCALE_STRING);

        return view('pages.index', 
        [
            'projetos' => $projetos,
            'docentes' => $this->docentes,
        ]);
    }
};
?>
<div class="space-y-6">
    <div class="relative z-10 p-6 mb-6 rounded-2xl shadow-lg relative overflow-hidden bg-portal-gradient">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                    <i class="fa fa-layer-group text-white text-2xl"></i>
                </div>
                <div zn_id="75">
                    <h1 class="text-2xl font-bold text-white mb-1">
                       Banco de Ofertas - Iniciação Científica EEL/USP
                    </h1>
                </div>
            </div>
        </div>
    </div>

    <x-portal::card>
        <div class="grid grid-cols-1 gap-4 md:grid">
            <x-portal::input
                label="Buscar"
                wire:model.live.debounce.300ms="search"
                placeholder="Busque por Projeto, Curso, Linha de Pesquisa..."
                wrapperClass="mb-0"
            />

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <x-portal::select
                    label="Docente"
                    wire:model.live="filterDocente"
                    :options="$docentes"
                    wrapperClass="mb-0"
                />

                <x-portal::select
                    label="Período"
                    wire:model.live="filterPeriodo"
                    :options="['' => 'Todos', 0 => 'Manhã', 1 => 'Tarde', 2 => 'Integral']"            
                    wrapperClass="mb-0"
                />

                <x-portal::select
                    label="Aluno Externo a USP"
                    wire:model.live="filterExterno"
                    :options="['' => 'Todos', 'S' => 'Sim', 'N' => 'Não']"
                    wrapperClass="mb-0"
                />
            </div> 

            <div class="flex flex-col justify-end">
                <x-portal::button full="true" variant="secondary" icon="fa-eraser" click="cleanFilters">Limpar filtros</x-portal::button>
            </div>
        </div>
    </x-portal::card>

    <x-portal::card padding="false">
        <x-portal::table>
            <x-slot:head>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Projeto</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Curso</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Linha de Pesquisa</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Docente</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Período</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Aceita Aluno Externo?</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Ações</th>
                </tr>
            </x-slot:head>

            <x-slot:body>
                @foreach($projetos as $projeto)
                    @php
                        $docente = Uspdev\Replicado\Pessoa::obterNome($projeto->codigoPessoa);
                    @endphp 

                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40">
                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">{{ $projeto->tituloProjeto }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $projeto->codigoCurso }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $projeto->linhaPesquisaProjeto }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $docente }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $projeto->periodoProjeto }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $projeto->statusExternoProjeto == 'S' ? 'Sim' : 'Não' }}</td>
                        <td class="px-4 py-3 text-right">
                            <x-portal::resource-actions
                                :viewHref="route('show', ['id' => $projeto->id])"
                                mode="label"
                            />
                        </td>
                    </tr>
                @endforeach
            </x-slot:body>
        </x-portal::table>
    </x-portal::card>    
</div>