<div class="md:col-span-2">
    <x-portal::input label="Projeto" name="nome" required />
</div>

<div class="md:col-span-2">
    <x-portal::textarea label="Descrição" name="descricaoProjeto" required></x-portal::textarea>
</div>

<div class="grid grid-cols-1 gap-4 md:grid-cols-4">
    <x-portal::select
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
    />
    
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