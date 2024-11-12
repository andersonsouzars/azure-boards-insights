<?php

require_once 'vendor/autoload.php';

use App\Utils\FileReader;
use App\Utils\EnvLoader;
use App\Exceptions\EnvLoaderException;

try {
    // Instanciar o leitor de arquivos
    $fileReader = new FileReader();

    // Instanciar o carregador de variáveis de ambiente
    $envLoader = new EnvLoader(__DIR__ . '/.env', $fileReader);

    // Carregar as variáveis
    $envLoader->load();

    // Exibir as variáveis carregadas
    
    echo "APP_ENV: " . EnvLoader::get('APP_ENV') . PHP_EOL;   // development
    
} catch (EnvLoaderException $e) {
    // Tratar erros de carregamento
    echo "Erro ao carregar variáveis de ambiente: " . $e->getMessage();
}
