<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Projeto;
use App\Models\Inscricao;
use App\Jobs\InscricaoProjetoJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Uspdev\Replicado\Pessoa;

new class extends Component
{
    use WithFileUploads;

    public Projeto $projeto;

    public $cursoInscricao;
    public $instituicaoInscricao;
    public $historicoEscolarInscricao;
    public bool $sucesso = false;

    // Regras de Validação
    protected function rules(): array
    {
        return 
        [
            'cursoInscricao'            => 'required|string|max:255',
            'instituicaoInscricao'      => 'required|string|max:255',
            'historicoEscolarInscricao' => 'required|file|mimes:pdf|max:10240',
        ];
    }

    protected function messages(): array
    {
        return 
        [
            'cursoInscricao.required'               => 'Curso é obrigatório.',
            'instituicaoInscricao.required'         => 'Instituição é obrigatório.',
            'historicoEscolarInscricao.required'    => 'É obrigatório enviar o Histórico Escolar em formato PDF.',
            'historicoEscolarInscricao.mimes'       => 'O histórico escolar deve ser um arquivo PDF.',
            'historicoEscolarInscricao.max'         => 'O arquivo não pode exceder 10 MB.',
        ];
    }

    public function mount(Projeto $projeto): void
    {
        $this->projeto  = $projeto->load('editais');
        $edital         = $this->projeto->editais->first();

        // Impede inscrições duplicadas do mesmo usuário no mesmo projeto
        $jaInscrito = Inscricao::where('codigoEdital', $edital->id)
            ->where('codigoUsuario', Auth::id())
            ->exists();

        if ($jaInscrito)
        {            
            session()->flash('error', 'Você já se inscreveu para este projeto.');
            redirect()->route('dashboard');
            return;
        }

        // Validação de edital aberto
        $hoje = Carbon::today();

        if (!$edital || !$edital->dataInicioEdital || !$edital->dataTerminoEdital) 
        {
            session()->flash('error', 'Este projeto não possui edital publicado.');
            redirect()->route('dashboard');
            return;
        }

        $inicio = Carbon::parse($edital->dataInicioEdital)->startOfDay();
        $fim = Carbon::parse($edital->dataTerminoEdital)->endOfDay();

        if ($hoje->lt($inicio) || $hoje->gt($fim)) {
            session()->flash('error', 'O período de inscrições para este projeto está encerrado.');
            redirect()->route('dashboard');
            return;
        }
    }

    public function save()
    {
        $dados  = $this->validate();

        $edital = $this->projeto->editais->first();

        // Impede inscrições duplicadas do mesmo usuário no mesmo projeto
        $jaInscrito = Inscricao::where('codigoEdital', $edital->id)
            ->where('codigoUsuario', Auth::id())
            ->exists();

        if ($jaInscrito)
        {
            session()->flash('error', 'Você já se inscreveu para este projeto.');
            redirect()->route('dashboard');
            return;
        }

        // 1. Armazena o Histórico Escolar
        $caminhoHistorico = $this->historicoEscolarInscricao->store('historicos_escolares', 'public');

        // 2. Registra a Inscrição vinculando ao user_id
        $inscricao = Inscricao::create([
            'codigoEdital'              => $edital->id,
            'codigoUsuario'             => Auth::id(),
            'cursoInscricao'            => $this->cursoInscricao,
            'instituicaoInscricao'      => $this->instituicaoInscricao,
            'historicoEscolarInscricao' => $caminhoHistorico,
            'statusInscricao'           => 'N',
        ]);

        // 3. Dispara o Job assíncrono para envio do e-mail na fila
        InscricaoProjetoJob::dispatch($this->projeto, $inscricao);

        session()->flash('success', 'Inscrição realizada com sucesso!');                
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('inscricoes.create');
    }
};
?>

@section('breadcrumbs')
    <a href="{{ Route::has('dashboard') ? route('dashboard') : '#' }}" class="hover:text-gray-700 hover:underline">Dashboard</a>
    <span>/</span>
    <span>Inscrição</span>
@endsection

<div class="space-y-6">
    <!-- Formulário Principal -->
    <form wire:submit.prevent="save" class="space-y-6" novalidate>
        @include('inscricoes.partials.form')
    </form>
</div>