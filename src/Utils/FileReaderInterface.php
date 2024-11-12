<?php

namespace App\Utils;

/**
 * Interface para manipulação de arquivos.
 * Define os métodos necessários para verificar a existência e ler o conteúdo de um arquivo.
 */
interface FileReaderInterface
{
    /**
     * Verifica se o arquivo especificado existe.
     *
     * @param string $filePath Caminho para o arquivo.
     * 
     * @return bool Retorna true se o arquivo existir, false caso contrário.
     */
    public function exists(string $filePath): bool;

    /**
     * Lê as linhas de um arquivo e retorna como um array.
     * Ignora linhas vazias e remove caracteres de nova linha.
     *
     * @param string $filePath Caminho para o arquivo.
     * 
     * @return array Um array contendo cada linha do arquivo.
     */
    public function readLines(string $filePath): array;
}
