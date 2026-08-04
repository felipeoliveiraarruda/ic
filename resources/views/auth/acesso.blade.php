@extends('portal-ui::layouts.guest')

@section('title', 'Acesso')

@section('content')

<div class="space-y-6">        
    <form class="space-y-4" method="POST" action="{{ route('login2') }}">
        @csrf
        <x-portal::input label="E-mail" name="email" type="email" placeholder="voce@exemplo.org" />
        <x-portal::input label="Senha" name="password" type="password" placeholder="••••••••" />
        <x-portal::button type="submit" full="true" icon="fa-sign-in-alt">Entrar</x-portal::button>
    </form>

    <x-portal::alert variant="info" title="Registro">
        Caso você não tenha registro no sistema clique no botão abaixo para fazer o seu registro.
    </x-portal::alert>

    <x-portal::button full="true" :href="route('register')" icon="fa-solid fa-user-plus">Registre-se</x-portal::button>
</div>
@endsection