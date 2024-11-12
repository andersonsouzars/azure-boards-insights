<?php

namespace App\Utils;

/**
 * Implementação padrão da interface FileReaderInterface.
 * Utiliza funções nativas do PHP para manipular arquivos.
 */
class FileReader implements FileReaderInterface
{
    /**
     * Verifica se o arquivo especificado existe.
     *
     * @param string $filePath Caminho para o arquivo.
     * 
     * @return bool Retorna true se o arquivo existir, false caso contrário.
     */
    public function exists(string $filePath): bool
    {
        return file_exists($filePath);
    }

    /**
     * Lê as linhas de um arquivo e retorna como um array.
     * Ignora linhas vazias e remove caracteres de nova linha.
     *
     * @param string $filePath Caminho para o arquivo.
     * 
     * @return array Um array contendo cada linha do arquivo.
     */
    public function readLines(string $filePath): array
    {
        return file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }
}
