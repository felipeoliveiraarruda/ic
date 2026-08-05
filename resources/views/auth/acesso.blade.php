@extends('portal-ui::layouts.guest')

@section('title', 'Acesso')

@section('content')

<div class="space-y-6">        
    <form class="space-y-4" method="POST" action="{{ route('login2') }}">
        @csrf
        <x-portal::input label="{{ __('Email') }}" name="email" type="email" placeholder="voce@exemplo.org" required/>
        <x-portal::input label="{{ __('Password') }}" name="password" type="password" placeholder="••••••••" required />
        <x-portal::button type="submit" full="true" icon="fa-sign-in-alt">Entrar</x-portal::button>
    </form>

    <x-portal::button full="true" :href="route('login')" variant="outline" icon="fa-sign-in-alt">Entrar com Senha Única USP</x-portal::button>
    <x-portal::button full="true" :href="route('auth/google/redirect')" variant="danger" icon="fa-brands fa-google">Entrar com Gmail</x-portal::button>

    <x-portal::alert variant="info" title="Registro">
        Caso você não tenha registro no sistema clique no botão.
    </x-portal::alert>

    <x-portal::button full="true" :href="route('register')" icon="fa-solid fa-user-plus">Registre-se</x-portal::button>
    
</div>
@endsection