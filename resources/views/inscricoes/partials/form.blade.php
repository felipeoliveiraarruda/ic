<x-portal::card>
    <x-slot:header>
        <div class="flex w-full flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3 min-w-0 flex-1">
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-portal-gradient text-white shadow-sm">
                    <i class="fa fa-info-circle text-sm"></i>
                </div>
                <div class="min-w-0">
                    <h2 class="text-base font-semibold text-gray-900 truncate">{{ $projeto->tituloProjeto }}</h2>
                    <p class="text-xs text-gray-500">Preencha as informações abaixo para concorrer a uma vaga desse projeto</p>
                </div>
            </div>

            <!-- Botões alinhados à direita -->
            <div class="flex items-center gap-2 flex-shrink-0 sm:ms-auto">
               <x-portal::button type="submit" icon="fa-solid fa-floppy-disk" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">Salvar</span>
                    <span wire:loading wire:target="save">Salvando...</span>
                </x-portal::button>

                <x-portal::button :href="route('dashboard')" variant="secondary" icon="fa-solid fa-arrow-rotate-left">
                    Cancelar
                </x-portal::button>
            </div>
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:grid-cols-3">
        <x-portal::input
            wire:model="cursoInscricao"
            label="Curso"
            placeholder="Engenharia de Materiais"
            required
        />

        <x-portal::input
            wire:model="instituicaoInscricao"
            label="Instituição"
            placeholder="Escola de Engenharia de Lorena - EEL/USP"
            required
        />

        <x-portal::input
            type="file"
            wire:model="historicoEscolarInscricao"
            label="Histórico Escolar"
            required
        />
    </div>
</x-portal::card>    