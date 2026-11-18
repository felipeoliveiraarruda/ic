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

        @include('pages.partials.detalhes')
    </div>
</div>