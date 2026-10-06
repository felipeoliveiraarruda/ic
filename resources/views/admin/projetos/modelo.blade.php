<?php
namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use App\Models\Projeto;
use App\Models\PreRequisito;

#[Layout('portal-ui::master')]
class GerenciarProjetos extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $busca = '';
    public bool $modalAberto = false;
    public ?int $projetoIdEmEdicao = null;

    #[Validate('required|integer')]
    public $codigoPessoaResponsavel;

    #[Validate('required|string|max:255')]
    public $tituloProjeto = '';

    #[Validate('nullable|string')]
    public $descricaoProjeto = '';

    #[Validate('nullable|string|max:255')]
    public $linhaPesquisaProjeto = '';

    #[Validate('nullable|string|max:255')]
    public $statusExternoProjeto = '';

    #[Validate('nullable|string|max:255')]
    public $tipoBolsaProjeto = '';

    #[Validate('nullable|string|max:255')]
    public $bolsaProjeto = '';

    #[Validate('required|integer|min:1')]
    public $numeroVagaProjeto = 1;

    #[Validate('required|date')]
    public $dataInicioProjeto = '';

    #[Validate('required|date|after_or_equal:dataInicioProjeto')]
    public $dataTerminoProjeto = '';

    #[Validate('nullable|string')]
    public $informacoesProjeto = '';

    // Campos do Pré-Requisito
    public ?int $projetoSelecionadoId = null;
    public $codigoCursoReq;
    public $periodoRequisito;

    public function abrirModalCriar(): void
    {
        $this->resetValidation();
        $this->reset(['projetoIdEmEdicao', 'tituloProjeto', 'descricaoProjeto', 'linhaPesquisaProjeto', 'statusExternoProjeto', 'tipoBolsaProjeto', 'bolsaProjeto', 'numeroVagaProjeto', 'dataInicioProjeto', 'dataTerminoProjeto', 'informacoesProjeto']);
        $this->codigoPessoaResponsavel = auth()->user()->codpes ?? auth()->user()->id;
        $this->modalAberto = true;
    }

    public function salvar(): void
    {
        $dados = $this->validate();

        if ($this->projetoIdEmEdicao) {
            Projeto::findOrFail($this->projetoIdEmEdicao)->update($dados);
            session()->flash('success', 'Projeto atualizado com sucesso!');
        } else {
            Projeto::create($dados);
            session()->flash('success', 'Projeto criado com sucesso!');
        }

        $this->modalAberto = false;
    }

    public function excluir(int $id): void
    {
        $projeto = Projeto::findOrFail($id);

        if ($projeto->editais()->exists()) {
            session()->flash('error', 'Não é possível excluir projetos vinculados a editais.');
            return;
        }

        $projeto->delete();
        session()->flash('success', 'Projeto removido com sucesso!');
    }

    public function adicionarPreRequisito(): void
    {
        $this->validate([
            'codigoCursoReq' => 'required|integer',
            'periodoRequisito' => 'required|string|max:255',
        ]);

        PreRequisito::create([
            'codigoProjeto' => $this->projetoSelecionadoId,
            'codigoCurso' => $this->codigoCursoReq,
            'periodoRequisito' => $this->periodoRequisito,
        ]);

        $this->reset(['codigoCursoReq', 'periodoRequisito']);
        session()->flash('success', 'Pré-requisito adicionado!');
    }

    public function render()
    {
        $projetos = Projeto::with('preRequisitos')
            ->when($this->busca, fn($q) => $q->where('tituloProjeto', 'like', "%{$this->busca}%"))
            ->latest()
            ->paginate(10);

        return <<<'HTML'
        <div>
            <div class="card card-outline card-primary">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold">Gestão de Projetos</h3>
                    <button wire:click="abrirModalCriar" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Novo Projeto
                    </button>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}</div>
                    @endif

                    <div class="mb-3">
                        <input type="text" wire:model.live.debounce.300ms="busca" class="form-control" placeholder="Buscar projeto por título...">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Título</th>
                                    <th>Vagas</th>
                                    <th>Vigência</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($projetos as $proj)
                                    <tr wire:key="proj-{{ $proj->id }}">
                                        <td>{{ $proj->id }}</td>
                                        <td><strong>{{ $proj->tituloProjeto }}</strong></td>
                                        <td>{{ $proj->numeroVagaProjeto }}</td>
                                        <td>{{ \Carbon\Carbon::parse($proj->dataInicioProjeto)->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($proj->dataTerminoProjeto)->format('d/m/Y') }}</td>
                                        <td>
                                            <button wire:click="excluir({{ $proj->id }})" wire:confirm="Excluir este projeto?" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center">Nenhum projeto cadastrado.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $projetos->links() }}
                </div>
            </div>

            @if($modalAberto)
            <div class="modal d-block bg-dark bg-opacity-50" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title font-weight-bold">Cadastrar Projeto</h5>
                            <button type="button" wire:click="$set('modalAberto', false)" class="close">&times;</button>
                        </div>
                        <form wire:submit="salvar">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Título do Projeto *</label>
                                    <input type="text" wire:model="tituloProjeto" class="form-control">
                                    @error('tituloProjeto') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label>Descrição</label>
                                    <textarea wire:model="descricaoProjeto" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <label>Nº de Vagas *</label>
                                        <input type="number" wire:model="numeroVagaProjeto" class="form-control">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label>Início *</label>
                                        <input type="date" wire:model="dataInicioProjeto" class="form-control">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label>Término *</label>
                                        <input type="date" wire:model="dataTerminoProjeto" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" wire:click="$set('modalAberto', false)" class="btn btn-secondary">Cancelar</button>
                                <button type="submit" class="btn btn-success">Salvar Projeto</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>
        HTML;
    }
}