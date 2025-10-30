import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/avaliacao.css',
                'resources/css/avaliacaoGuide.css',
                'resources/css/demanda_cadastro.css',
                'resources/css/demandas.css',
                'resources/css/esqueciAsenha.css',
                'resources/css/guide.css',
                'resources/css/guideliness.css',
                'resources/css/login.css',
                'resources/css/problema.css',
                'resources/css/registro.css',
                'resources/css/resetarSenha.css',
                'resources/css/sessao.css',
                'resources/css/style.css',

                'resources/js/app.js',
                'resources/js/avaliacao.js',
                'resources/js/avaliacaoGuide.js',
                'resources/js/bootstrap.js',
                'resources/js/demanda_create.js',
                'resources/js/demanda_ver.js',
                'resources/js/guideliness.js',
                'resources/js/problema.js',
                'resources/js/sessao.js',
                'resources/js/teste.js',
            ],
            refresh: true,
        }),
    ],
});