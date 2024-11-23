<?php

namespace App\Inicializacao;

use App\Utils\EnvLoader;
use App\Utils\FileReader;

class EnvBootstrapper
{
    public static function initialize(): void
    {
        static $initialized = false;

        if (!$initialized) {
            try {
                $envLoader = new EnvLoader(__DIR__ . '/../../.env', new FileReader());
                $envLoader->load();
                $initialized = true;
            } catch (\Throwable $th) {
                die($th->getMessage());
            }
        }
    }
}

// Chama o método de inicialização automaticamente
EnvBootstrapper::initialize();