<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('checklists')->truncate();
        DB::table('checklists')->insert([
            [
                'id' => 1,
                'nome' => 'Imagens',
                'descricao' => 'A acessibilidade em imagens permite que todos os usuários, incluindo aqueles que usam tecnologia assistiva, possam compreender e utilizar a interface que faz uso de imagens tanto para transmitir informação quanto para executar uma ação.',
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 2,
                'nome' => 'Áudio e vídeo',
                'descricao' => 'Verificação e otimização de aspectos específicos para áudio e vídeo, garantindo sua acessibilidade e qualidade.',
                'created_at' => now(), 'updated_at' => now()
            ],
        ]);

        DB::table('diretrizes')->truncate();
        DB::table('diretrizes')->insert([
            [
                'id' => 1,
                'codigo' => '1.1',
                'nome' => 'Alternativas de texto',
                'descricao' => 'Forneça alternativas de texto para qualquer conteúdo não textual',
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 2,
                'codigo' => '1.2',
                'nome' => 'Mídias com base em tempo',
                'descricao' => 'Fornecer alternativas para mídias baseadas em tempo.',
                'created_at' => now(), 'updated_at' => now()
            ],
        ]);

        DB::table('itens')->truncate();
        DB::table('itens')->insert([
            [
                'id' => 1, 'checklist_id' => 1,
                'descricao' => 'Todas as imagens que transmitem informação ou conteúdo relevante têm texto alternativo que descreve a imagem.',
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 2, 'checklist_id' => 1,
                'descricao' => 'Todas as imagens funcionais (para uso em botões e links, por exemplo) têm texto alternativo que descreve a funcionalidade da imagem (e não a própria imagem).',
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 3, 'checklist_id' => 1,
                'descricao' => 'Todas as imagens decorativas têm texto alternativo nulo ou não estão no código HTML, e podem ser ignoradas por tecnologia assistiva.',
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 4, 'checklist_id' => 1,
                'descricao' => 'Todas as imagens complexas (como gráficos) têm texto alternativo e descrição disponível na página (próximo à imagem) ou em outro link.',
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 5, 'checklist_id' => 1,
                'descricao' => 'Mapas de imagens têm texto alternativo para cada área interativa',
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 6, 'checklist_id' => 1,
                'descricao' => 'Não há texto essencial ou necessário para compreensão do documento em formato de imagem. OU (item a seguir)',
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 7, 'checklist_id' => 1,
                'descricao' => "Todo conteúdo apresentado está em formato de texto ou\r\nTodas as imagens de texto são consideradas essenciais e possuem texto alternativo com o mesmo conteúdo.",
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 8, 'checklist_id' => 2,
                'descricao' => 'Todo áudio pré-gravado tem uma alternativa em texto que transcreve todo o conteúdo do áudio.',
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 9, 'checklist_id' => 2,
                'descricao' => 'Todo vídeo sem áudio pré-gravado tem uma alternativa em texto que transcreve todo o conteúdo do vídeo.',
                'created_at' => now(), 'updated_at' => now()
            ],
        ]);

        DB::table('criterios')->truncate();
        DB::table('criterios')->insert([
            [
                'id' => 1, 'diretriz_id' => 1,
                'codigo' => '1.1.1', 'nome' => 'Conteúdo Não Textual', 'conformidade' => 'A',
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 2, 'diretriz_id' => 2,
                'codigo' => '1.2.1', 'nome' => 'Apenas Áudio e Apenas Vídeo (Pré-gravado)', 'conformidade' => 'A',
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 3, 'diretriz_id' => 2,
                'codigo' => '1.2.2', 'nome' => 'Legendas (Pré-gravadas)', 'conformidade' => 'A',
                'created_at' => now(), 'updated_at' => now()
            ],
        ]);

        DB::table('criterio_item')->truncate();
        DB::table('criterio_item')->insert([
            ['id' => 1, 'criterio_id' => 1, 'item_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'criterio_id' => 1, 'item_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'criterio_id' => 1, 'item_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'criterio_id' => 1, 'item_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'criterio_id' => 1, 'item_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'criterio_id' => 2, 'item_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'criterio_id' => 2, 'item_id' => 9, 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::enableForeignKeyConstraints();
    }
}