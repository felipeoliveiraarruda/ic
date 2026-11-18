<?php
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;
use App\Models\Utils;
use App\Models\Projeto;

new class extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public function mount()
    {   
        if (Auth::check()) 
        {
            Utils::setSession(Auth::user()->id);

            if (session('level') === 'admin' || in_array('Docente', session('vinculos'))) 
            {
                return $this->redirectRoute('admin.dashboard');
            }

            if (in_array('Outros', session('vinculos')) || in_array('Alunogr', session('vinculos'))) 
            {                
                if (session()->has('url.intended')) 
                {
                    return redirect()->intended();
                }

               return $this->redirectRoute('dashboard');
            }
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function cleanFilters(): void
    {
        $this->reset('search');
        $this->resetPage();
    }

    public function render()
    {
        $projetos = Projeto::query()
            ->with(['editais', 'cursos'])
            ->when($this->search, function ($query) 
            {
                $searchTerm = '%' . trim($this->search) . '%';
                $termoLower = mb_strtolower(trim($this->search), 'UTF-8');

                // Consulta direta e ultrarrápida via SQL local
                $query->where(function ($q) use ($searchTerm, $statusExternoBusca) {
                    $q->where('tituloProjeto', 'like', $searchTerm)
                      ->orWhere('linhaPesquisaProjeto', 'like', $searchTerm)
                      ->orWhere('codigoPessoaResponsavel', 'like', $searchTerm)
                      ->orWhere('nomePessoaResponsavel', 'like', $searchTerm);


                    $q->orWhereHas('cursos', function ($cursoQuery) use ($searchTerm) {
                        $cursoQuery->where('codigoCurso', 'like', $searchTerm)
                                   ->orWhere('nomeCurso', 'like', $searchTerm);
                    });
                });
            })
            ->where('dataInicioProjeto', '>=', date('Y-m-d'))
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pages.index', 
        [
            'projetos' => $projetos,
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
                <div>
                    <h1 class="text-2xl font-bold text-white mb-1">
                       Banco de Ofertas - Iniciação Científica EEL/USP
                    </h1>
                </div>
            </div>
        </div>
    </div>

    @include('pages.partials.lista')
</div>