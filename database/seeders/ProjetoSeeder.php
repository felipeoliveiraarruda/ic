<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProjetoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projetos = [];
        $cursos = [
            'Engenharia Química', 
            'Engenharia Bioquímica', 
            'Engenharia de Materiais', 
            'Engenharia Ambiental',
            'Engenharia Física',
            'Engenharia de Produção',
        ];

        $docentes = [
        "101761", 
        "1033242", 
        "11028929", 
        "11079086", 
        "1112574", 
        "1176388", 
        "1285870", 
        "1304060", 
        "1341653", 
        "14466927", 
        "1488970", 
        "1506103", 
        "15905657", 
        "1643715", 
        "1720367", 
        "1814052", 
        "198273", 
        "210064", 
        "2143261", 
        "2166002", 
        "2256396", 
        "229266", 
        "230696", 
        "2341641", 
        "2342277", 
        "2346890", 
        "3268262", 
        "3295113", 
        "3380737", 
        "3403572", 
        "3444370", 
        "3454277", 
        "3577649", 
        "3586455", 
        "3682251", 
        "427823", 
        "471420", 
        "4780627", 
        "4808662", 
        "4873328", 
        "4893449", 
        "4894221", 
        "5009972", 
        "5082401", 
        "5111420", 
        "519033", 
        "5464150", 
        "5536171", 
        "5729033", 
        "5816812", 
        "5817045", 
        "5817066", 
        "5817181", 
        "5817330", 
        "5817344", 
        "5817372", 
        "5817535", 
        "5817650", 
        "5817692", 
        "5817712", 
        "5818710", 
        "5840521", 
        "5840535", 
        "5840577", 
        "5840581", 
        "5840598", 
        "5840622", 
        "5840639", 
        "5840650", 
        "5840671", 
        "5840692", 
        "5840705", 
        "5840712", 
        "5840726", 
        "5840730", 
        "5840751", 
        "5840768", 
        "5840772", 
        "5840793", 
        "5840809", 
        "5840813", 
        "5840820", 
        "5840841", 
        "5840876", 
        "5840880", 
        "5840897", 
        "5840900", 
        "5840917", 
        "5840938", 
        "5840942", 
        "5963230", 
        "5983729", 
        "6007846", 
        "6046221", 
        "6270264", 
        "6279110", 
        "6310296", 
        "6310316", 
        "63630", 
        "6495737", 
        "6666306", 
        "6712818", 
        "6751718", 
        "7043088", 
        "7129830", 
        "7236913", 
        "7455355", 
        "7459752", 
        "7516317", 
        "7565278", 
        "7811306", 
        "787307", 
        "7926291", 
        "8151869", 
        "8188658", 
        "8426375", 
        "849935", 
        "8554681", 
        "8643537", 
        "8711290", 
        "8711623", 
        "8711686", 
        "8767640", 
        "8822123", 
        "8853480", 
        "8855158", 
        "8870322", 
        "8971158", 
        "9146830", 
        "9149242", 
        "951832", 
        "984972", 
        "998313", 
        ];

        $linhas   = ['IA e Aprendizado de Máquina', 'Sistemas Distribuídos', 'Educação Tecnológica'];        
        $periodos = ['Manhã', 'Tarde', 'Integral'];
        $tipos    = ['Com Bolsa', 'Sem Bolsa', 'Possível Bolsa'];
        $bolsas   = ['PIBIC', 'PIBITI', 'Voluntário', 'Financiamento Externo'];

        for ($i = 1; $i <= 20; $i++) {
            $docente = $docentes[array_rand($docentes)];
            $tipoBolsa = $tipos[array_rand($tipos)];

            $projetos[] = [
                'codigoPessoa'          => $docente,
                'codigoCurso'           => $cursos[array_rand($cursos)],
                'tituloProjeto'         => "Projeto de Extensão " . Str::random(5),
                'periodoProjeto'        => $periodos[array_rand($periodos)],
                'linhaPesquisaProjeto'  => $linhas[array_rand($linhas)],
                'statusExternoProjeto'  => rand(0, 1) ? 'S' : 'N',
                'tipoBolsaProjeto'      => $tipoBolsa,
                'bolsaProjeto'          => $tipoBolsa == 'Com Bolsa' ? $bolsas[array_rand($bolsas)] : NULL,
                'dataInicioProjeto'     => now()->subMonths(rand(1, 6))->format('Y-m-d'),
                'dataTerminoProjeto'    => now()->addMonths(rand(6, 12))->format('Y-m-d'),
                'descricaoProjeto'      => "Descrição detalhada sobre as atividades do projeto {$i}.",
                'informacoesProjeto'    => "Informações adicionais e requisitos do projeto {$i}.",
                'codigoPessoaCriacao'   => $docente,
                'codigoPessoaAlteracao' => $docente,
                'created_at'            => now(),
                'updated_at'            => now(),
                'deleted_at'            => null,
            ];            
        }

        DB::table('projetos')->insert($projetos);
    }
}
