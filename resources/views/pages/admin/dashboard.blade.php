<?php

use Livewire\Component;

new class extends Component
{
    public function render()
    {
        return view('pages.admin');
    }
};
?>

@section('breadcrumbs')
    <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : '#' }}" class="hover:text-gray-700 hover:underline">Dashboard</a>
    <span>/</span>
    <span>Projeto</span>
@endsection

<div class="space-y-6">
   Dashboard
</div>