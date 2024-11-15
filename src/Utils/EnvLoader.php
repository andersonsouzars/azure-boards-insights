<?php

declare(strict_types=1);

namespace App\Utils;

use App\Exceptions\EnvLoaderException;

/**
 * Classe responsável por carregar e gerenciar variáveis de ambiente a partir de arquivos .env.
 */
class EnvLoader
{
    /**
     * Caminho para o arquivo .env.
     *
     * @var string
     */
    private string $filePath;

    /**
     * Leitor de arquivos que implementa a interface FileReaderInterface.
     *
     * @var FileReaderInterface
     */
    private FileReaderInterface $fileReader;

    /**
     * Construtor da classe.
     *
     * @param string $filePath Caminho para o arquivo .env.
     * @param FileReaderInterface $fileReader Implementação da interface para leitura de arquivos.
     * 
     * @throws EnvLoaderException Se o arquivo .env não existir.
     */
    public function __construct(string $filePath, FileReaderInterface $fileReader)
    {
        if (!$fileReader->exists($filePath)) {
            throw new EnvLoaderException("Arquivo .env não encontrado no caminho especificado: {$filePath}");
        }

        $this->filePath = $filePath;
        $this->fileReader = $fileReader;
    }

    /**
     * Carrega as variáveis de ambiente do arquivo .env.
     *
     * @return void
     */
    public function load(): void
    {
        $lines = $this->fileReader->readLines($this->filePath);

        foreach ($lines as $line) {
            $this->processEnvLine($line);
        }
    }

    /**
     * Processa uma linha do arquivo .env, definindo variáveis de ambiente.
     *
     * @param string $line Linha do arquivo a ser processada.
     * 
     * @return void
     */
    private function processEnvLine(string $line): void
    {
        $line = trim($line);

        if ($this->isCommentOrEmpty($line)) {
            return;
        }

        [$key, $value] = $this->parseLine($line);

        $this->setEnvironmentVariable($key, $value);
    }

    /**
     * Verifica se uma linha é um comentário ou está vazia.
     *
     * @param string $line Linha a ser verificada.
     * 
     * @return bool Retorna true se for um comentário ou linha vazia, false caso contrário.
     */
    private function isCommentOrEmpty(string $line): bool
    {
        return empty($line) || str_starts_with($line, '#');
    }

    /**
     * Faz o parse de uma linha do arquivo para extrair a chave e o valor.
     *
     * @param string $line Linha a ser processada.
     * 
     * @return array Array contendo a chave e o valor [key, value].
     * 
     * @throws EnvLoaderException Se o formato da linha for inválido.
     */
    private function parseLine(string $line): array
    {
        $parts = explode('=', $line, 2);

        if (count($parts) !== 2) {
            throw new EnvLoaderException("Formato inválido no arquivo .env: {$line}");
        }

        $key = trim($parts[0]);
        $value = $this->sanitizeValue(trim($parts[1]));

        return [$key, $value];
    }

    /**
     * Remove aspas ao redor do valor, se existirem.
     *
     * @param string $value Valor a ser sanitizado.
     * 
     * @return string Valor sanitizado.
     */
    private static function sanitizeValue(string $value): string
    {
        if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))
        ) {
            return trim($value, "\"'");
        }

        return $value;
    }

    /**
     * Define uma variável de ambiente.
     *
     * @param string $key Chave da variável.
     * @param string $value Valor da variável.
     * 
     * @return void
     */
    private function setEnvironment(string $key, string $value): void
    {
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }

    /**
     * Define uma variável de ambiente, resolvendo referências no valor.
     *
     * @param string $key Chave da variável.
     * @param string $value Valor da variável, que pode conter referências.
     * 
     * @return void
     */
    private function setEnvironmentVariable(string $key, string $value): void
    {
        // Resolva referências dentro do valor
        $resolvedValue = $this->resolveReferences($value);

        // Defina a variável no ambiente
        $this->setEnvironment($key, $resolvedValue);
    }

    /**
     * Resolve referências a outras variáveis no valor.
     *
     * @param string $value Valor que pode conter referências no formato {VAR}.
     * 
     * @return string Valor com as referências resolvidas.
     */
    private function resolveReferences(string $value): string
    {
        return preg_replace_callback('/\{(\w+)\}/', function ($matches) {
            $referenceKey = $matches[1];
            return $_ENV[$referenceKey] ?? $_SERVER[$referenceKey] ?? $matches[0];
        }, $value);
    }

    /**
     * Recupera o valor de uma variável de ambiente.
     *
     * @param string $key Chave da variável.
     * @param mixed $default Valor padrão caso a variável não esteja definida.
     * 
     * @return mixed Valor da variável de ambiente ou o valor padrão.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;
    }
}
