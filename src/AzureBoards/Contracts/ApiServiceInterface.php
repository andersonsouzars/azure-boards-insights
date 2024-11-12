<?php

namespace App\AzureBoards\Contracts;

/**
 * Interface ApiServiceInterface
 *
 * Define o contrato para serviços que executam operações na API do Azure Boards.
 * Cada implementação desta interface deve encapsular a lógica necessária para interagir com endpoints específicos.
 *
 * @package App\AzureBoards\Contracts
 */
interface ApiServiceInterface
{
    /**
     * Executa uma operação específica na API do Azure Boards.
     *
     * @param array $params Parâmetros necessários para executar a operação. O formato depende da implementação específica.
     *
     * @return array Retorna a resposta da API como um array JSON decodificado.
     */
    public function execute(array $params): array;
}
