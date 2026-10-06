<?php

use Livewire\Component;
use App\Models\Projeto;
use App\Models\Curso;
use App\Models\Edital;
use Uspdev\Replicado\Pessoa;
use Uspdev\Replicado\Graduacao;

new class extends Component
{
    public function render()
    {
        return view('admin.index');
    }
};
?>

@section('breadcrumbs')
    <span>Dashboard</span>
@endsection

<div class="space-y-6">
   
</div>