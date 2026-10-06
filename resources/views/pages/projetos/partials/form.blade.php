<div class="md:col-span-2">
    <x-portal::input label="Projeto" name="nome" required />
</div>

<div class="md:col-span-2">
    <x-portal::textarea label="Descrição" name="descricaoProjeto" required></x-portal::textarea>
</div>

<div class="grid grid-cols-1 gap-4 md:grid-cols-4">
    <!--<x-portal::select
        label="Curso"
        name="codigoCurso"
        :options="[
                'Engenharia Química'      => 'Engenharia Química', 
                'Engenharia Bioquímica'   => 'Engenharia Bioquímica', 
                'Engenharia de Materiais' => 'Engenharia de Materiais', 
                'Engenharia Ambiental'    => 'Engenharia Ambiental',
                'Engenharia Física'       => 'Engenharia Física',
                'Engenharia de Produção'  => 'Engenharia de Produção'
            ]"
        required
    />-->
    
    <x-portal::input label="Linha de Pesquisa" name="linhaPesquisaProjeto" required />

    <x-portal::input label="Período" name="periodoProjeto" required />

    <x-portal::select
        label="Aceita Aluno Externo a USP"
        name="statusExternoProjeto"
        :options="[
                'Não'      => 'Não', 
                'Sim'   => 'Sim', 
            ]"
        required
    />
</div>            

<div class="grid grid-cols-1 gap-4 md:grid-cols-4 md:col-span-2">
    <x-portal::select
        label="Bolsa"
        name="tipoBolsaProjeto"
        :options="[
                'Com Bolsa'      => 'Com Bolsa', 
                'Sem Bolsa'      => 'Sem Bolsa', 
                'Possível Bolsa' => 'Possível Bolsa', 
            ]"
        required
    />
    
    <x-portal::input label="Tipo de Bolsa" name="bolsaProjeto"  />

    <x-portal::input type="date" label="Início do Projeto" name="dataInicioProjeto" required />

    <x-portal::input type="date" label="Término do Projeto" name="dataTerminoProjeto" required />
</div>   

<div class="md:col-span-2">
    <x-portal::textarea label="Informações" name="informacoesProjeto" required></x-portal::textarea>
</div>



<!--
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-portal::input 
                    label="Projeto" 
                    name="tituloProjeto" 
                    wire:model="tituloProjeto" 
                    required 
                />

                <x-portal::select
                    label="Responsável"
                    wire:model="codigoPessoa"
                    :options="$this->docentesOptions"
                    wrapperClass="mb-0"
                    required
                />                
            </div>

            <div class="md:col-span-2">
                <x-portal::textarea 
                    label="Descrição" 
                    name="descricaoProjeto" 
                    wire:model="descricaoProjeto" 
                    required>
                </x-portal::textarea>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <x-portal::select
                    label="Curso"
                    name="codigoCurso"
                    wire:model="codigoCurso"
                    :options="[
                        'Engenharia Química'      => 'Engenharia Química', 
                        'Engenharia Bioquímica'   => 'Engenharia Bioquímica', 
                        'Engenharia de Materiais' => 'Engenharia de Materiais', 
                        'Engenharia Ambiental'    => 'Engenharia Ambiental',
                        'Engenharia Física'       => 'Engenharia Física',
                        'Engenharia de Produção'  => 'Engenharia de Produção'
                    ]"
                    required
                />
                
                <x-portal::input 
                    label="Linha de Pesquisa" 
                    name="linhaPesquisaProjeto" 
                    wire:model="linhaPesquisaProjeto" 
                    required 
                />

                <x-portal::input 
                    label="Período" 
                    name="periodoProjeto" 
                    wire:model="periodoProjeto" 
                    required 
                />

                <x-portal::select
                    label="Aceita Aluno Externo a USP"
                    name="statusExternoProjeto"
                    wire:model="statusExternoProjeto"
                    :options="[
                        'N' => 'Não', 
                        'S' => 'Sim', 
                    ]"
                    required
                />
            </div>            

            <div class="grid grid-cols-1 gap-4 md:grid-cols-4 md:col-span-2">
                <x-portal::select
                    label="Bolsa"
                    name="tipoBolsaProjeto"
                    wire:model.live="tipoBolsaProjeto"
                    :options="[
                        'Com Bolsa'      => 'Com Bolsa', 
                        'Sem Bolsa'      => 'Sem Bolsa', 
                        'Possível Bolsa' => 'Possível Bolsa', 
                    ]"
                    required
                />
                
                <x-portal::input 
                    label="Tipo de Bolsa" 
                    name="bolsaProjeto" 
                    wire:model="bolsaProjeto"
                    :disabled="$tipoBolsaProjeto !== 'Com Bolsa'"
                />

                <x-portal::input 
                    type="date" 
                    label="Início do Projeto" 
                    name="dataInicioProjeto" 
                    wire:model="dataInicioProjeto" 
                    required 
                />

                <x-portal::input 
                    type="date" 
                    label="Término do Projeto" 
                    name="dataTerminoProjeto" 
                    wire:model="dataTerminoProjeto" 
                    required 
                />
            </div>   

            <div class="md:col-span-2">
                <x-portal::textarea 
                    label="Informações" 
                    name="informacoesProjeto" 
                    wire:model="informacoesProjeto">
                </x-portal::textarea>
            </div>
  
            <div class="md:col-span-2 flex flex-col gap-3 border-t border-gray-200 pt-4 sm:flex-row sm:justify-end dark:border-gray-700">

-->

<!--<div class="grid grid-cols-1 gap-4 md:grid-cols-4">
    <div class="md:col-span-2">
        <x-portal::textarea 
            label="Descrição do Projeto" 
            name="descricaoProjeto" 
            wire:model="descricaoProjeto" 
            required>
        </x-portal::textarea>
    </div>

    <div class="md:col-span-2">
        <x-portal::textarea 
            label="Informações do Projeto" 
            name="informacoesProjeto" 
            wire:model="informacoesProjeto">
        </x-portal::textarea>
    </div>
</div> 

<div class="grid grid-cols-1 gap-4 md:grid-cols-4">
    <x-portal::input 
        label="Linha de Pesquisa" 
        name="linhaPesquisaProjeto" 
        wire:model="linhaPesquisaProjeto" 
        required 
    />

    <x-portal::select
        label="Aceita Aluno Externo a USP"
        name="statusExternoProjeto"
        wire:model="statusExternoProjeto"
        :options="[
            'N' => 'Não', 
            'S' => 'Sim', 
        ]"
        required
    />

    <x-portal::select
        label="Bolsa"
        name="tipoBolsaProjeto"
        wire:model.live="tipoBolsaProjeto"
        :options="[
            'Com Bolsa'      => 'Com Bolsa', 
            'Sem Bolsa'      => 'Sem Bolsa', 
            'Possível Bolsa' => 'Possível Bolsa', 
        ]"
        required
    />
    
    <x-portal::input 
        label="Tipo de Bolsa" 
        name="bolsaProjeto" 
        wire:model="bolsaProjeto"
        :disabled="$tipoBolsaProjeto !== 'Com Bolsa'"
    />

    <div class="md:col-span-2">
        <x-portal::textarea 
            label="Informações do Projeto" 
            name="informacoesProjeto" 
            wire:model="informacoesProjeto">
        </x-portal::textarea>
    </div>    

</div>-->