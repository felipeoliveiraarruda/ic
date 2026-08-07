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
            'Pós-Graduação em Engenharia de Materiais',
            'Pós-Graduação em Biotecnologia Industrial',
            'Pós-Graduação em Engenharia Química',
            'Mestrado Profissional Projetos Educacionais de Ciências',
            'Pós-Graduação em Meio Ambiente e Desenvolvimento'
        ];
        $periodos = ['Manhã', 'Tarde', 'Integral'];

        for ($i = 1; $i <= 20; $i++) {
            $projetos[] = [
                'codigoCurso'        => $cursos[array_rand($cursos)],
                'tituloProjeto'      => "Projeto de Extensão " . Str::random(5),
                'descricaoProjeto'   => "Descrição detalhada sobre as atividades do projeto número {$i}.",
                'numeroHorasProjeto' => (string) rand(20, 120),
                'periodoProjeto'     => $periodos[array_rand($periodos)],
                'informacoesProjeto' => "Informações adicionais e requisitos do projeto {$i}.",
                'created_at'         => now(),
                'updated_at'         => now(),
                'deleted_at'         => null,
            ];
        }

        DB::table('projetos')->insert($projetos);
    }
}
