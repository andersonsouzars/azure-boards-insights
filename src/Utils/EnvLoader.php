<?php

declare(strict_types=1);

namespace App\Utils;

use RuntimeException;

class EnvLoader {
    private string $filePath;

    public function __construct(string $filePath) {
        if (!file_exists($filePath)) {
            throw new RuntimeException("Arquivo .env não encontrado no caminho especificado: {$filePath}");
        }

        $this->filePath = $filePath;
    }

    public function load(): void {
        $lines = file($this->filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);

            // Ignorar comentários e linhas em branco
            if ($this->isCommentOrEmpty($line)) {
                continue;
            }

            // Separar chave e valor
            [$key, $value] = $this->parseLine($line);

            // Adicionar ao ambiente se ainda não existir
            $this->setEnvironmentVariable($key, $value);
        }
    }

    private function isCommentOrEmpty(string $line): bool {
        return empty($line) || str_starts_with($line, '#');
    }

    private function parseLine(string $line): array {
        $parts = explode('=', $line, 2);

        if (count($parts) !== 2) {
            throw new RuntimeException("Formato inválido no arquivo .env: {$line}");
        }

        $key = trim($parts[0]);
        $value = $this->sanitizeValue(trim($parts[1]));

        return [$key, $value];
    }

    private function sanitizeValue(string $value): string {
        // Remover aspas ao redor do valor, se existirem
        if (str_starts_with($value, '"') && str_ends_with($value, '"')) {
            return trim($value, '"');
        }

        if (str_starts_with($value, "'") && str_ends_with($value, "'")) {
            return trim($value, "'");
        }

        return $value;
    }

    private function setEnvironmentVariable(string $key, string $value): void {
        if (!array_key_exists($key, $_ENV) && !array_key_exists($key, $_SERVER)) {
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }

    public static function get(string $key, mixed $default = null): mixed {
        return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;
    }
}
