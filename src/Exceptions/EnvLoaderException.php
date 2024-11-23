<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Exceção personalizada para erros relacionados ao carregamento de arquivos .env.
 *
 * Essa exceção é usada para indicar problemas específicos durante o processamento
 * de variáveis de ambiente, como arquivos inexistentes ou linhas mal formatadas.
 */
class EnvLoaderException extends RuntimeException
{
    public function customErrorMessage(): string
    {
        return "EnvLoaderException: " . $this->getMessage();
    }

}
