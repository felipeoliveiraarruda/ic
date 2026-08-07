@extends('portal-ui::layouts.guest')

@section('title', 'Registro')

@section('content')

<div class="space-y-6">
    <form class="space-y-4" method="POST" action="{{ route('register') }}">
        @csrf
                
        <x-portal::input label="{{ __('Name') }}" name="name" type="text" :value="old('name')" placeholder="José dos Santos" required autocomplete="name" />

        <x-portal::input label="{{ __('Email') }}" name="email" type="email" placeholder="voce@exemplo.org" required/>

        <x-portal::input label="{{ __('Password') }}" name="password" type="password" placeholder="••••••••" required autocomplete="new-password" />

        <x-portal::input label="{{ __('Confirm Password') }}" name="password_confirmation" type="password" placeholder="••••••••" required autocomplete="new-password" />
        
        <x-portal::button type="submit" full="true" icon="fa-solid fa-user-plus">Registrar</x-portal::button>
    </form>

    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('local') }}">
        {{ __('Already registered?') }}
    </a>
</div>

@endsection
