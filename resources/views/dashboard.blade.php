@extends('portal-ui::layouts.app')

@section('title', 'Dashboard')

@section('content')
    <x-portal::page-header
        title="{{ __('Dashboard') }}"
        subtitle="Resumo da operação"
    />

    <x-portal::card>
        <x-slot:header>
           {{ __('Dashboard') }}
        </x-slot:header>

        {{ __('Dashboard') }}
    </x-portal::card>
@endsection
