<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Projeto;

new class extends Component
{
    use WithPagination;

    // Propriedades para controle do modal de exclusão
    public bool $confirmingDeletion = false;
    public ?int $projetoIdParaExcluir = null;
    public ?string $tituloProjetoParaExcluir = '';

    /**
     * Abre o modal e define o projeto a ser excluído
     */
    public function confirmDelete(int $id): void
    {
        $projeto = Projeto::findOrFail($id);

        // Trava de segurança: garante que o usuário comum só possa excluir o próprio projeto
        if (session('level') == 'user' && auth()->user()->codpes != $projeto->codigoPessoaCriacao) {
            return;
        }

        $this->projetoIdParaExcluir = $projeto->id;
        $this->tituloProjetoParaExcluir = $projeto->tituloProjeto;
        $this->confirmingDeletion = true;
    }

    /**
     * Executa a exclusão do projeto selecionado
     */
    public function deleteProject(): void
    {
        if ($this->projetoIdParaExcluir) {
            $projeto = Projeto::find($this->projetoIdParaExcluir);

            if ($projeto) {
                // Validação de permissão antes de deletar
                if (session('level') == 'admin' || auth()->user()->codpes == $projeto->codigoPessoaCriacao) {
                    $projeto->delete();
                    session()->flash('success', 'Projeto excluído com sucesso!');
                }
            }
        }

        $this->cancelDelete();
    }

    /**
     * Fecha o modal e limpa as variáveis de estado
     */
    public function cancelDelete(): void
    {
        $this->confirmingDeletion = false;
        $this->projetoIdParaExcluir = null;
        $this->tituloProjetoParaExcluir = '';
    }

    public function render()
    {
        return view('pages.projetos.index',
        [
            'projetos'  => (session('level') == 'user' ? Projeto::where('codigoPessoaCriacao', auth()->user()->codpes)->paginate(10) : Projeto::all()),
            'level'     => session('level'),
        ]);
    }
};
?>

@section('breadcrumbs')
    <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : '#' }}" class="hover:text-gray-700 hover:underline">Dashboard</a>
    <span>/</span>
    <span>Projeto</span>
@endsection

<div class="space-y-6">
    <x-portal::page-header
        title="Projetos"
        subtitle="Visão operacional dos projetos cadastrados.">

        <x-slot:actions>
            <x-portal::button icon="fa-plus" :href="route('admin.projetos.create')">Novo Projeto IC</x-portal::button>
        </x-slot:actions>

    </x-portal::page-header>

    <x-portal::flash-messages />

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
                            @if(auth()->user()->codpes == $projeto->codigoPessoaCriacao)
                            <x-portal::resource-actions
                                :viewHref="route('admin.projetos.show', ['id' => $projeto->id])"
                                :editHref="route('admin.projetos.edit', ['projeto' => $projeto->id])"
                            />
                            @else
                            <x-portal::resource-actions
                                :viewHref="route('admin.projetos.show', ['id' => $projeto->id])"
                            />
                            @endif
                        </td>
                    </tr>
                @endforeach
            </x-slot:body>
        </x-portal::table>
    </x-portal::card>

    <!-- Modal de Confirmação de Exclusão -->
    @if($confirmingDeletion)

    @endif    
</div>