<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

@section('breadcrumbs')
    <a href="{{ Route::has('dashboard') ? route('dashboard') : '#' }}" class="hover:text-gray-700 hover:underline">Dashboard</a>
@endsection

<div>
    {{-- Simplicity is the ultimate sophistication. - Leonardo da Vinci --}}
</div>