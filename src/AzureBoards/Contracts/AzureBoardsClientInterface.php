<?php

namespace App\AzureBoards\Contracts;

/**
 * Interface AzureBoardsClientInterface
 *
 * Define o contrato para o cliente que interage com a API do Azure Boards.
 * Este cliente é responsável por realizar requisições HTTP autenticadas.
 *
 * @package App\AzureBoards\Contracts
 */
interface AzureBoardsClientInterface
{
    /**
     * Faz uma requisição para a API do Azure Boards.
     *
     * @param string $method O método HTTP a ser utilizado (ex.: "GET", "POST", "PUT", "DELETE").
     * @param string $url A URL completa do endpoint da API do Azure Boards.
     * @param array $options Parâmetros opcionais para a requisição, como cabeçalhos, corpo e query strings.
     *
     * @return array Retorna a resposta da API como um array JSON decodificado.
     */
    public function request(string $method, string $url, array $options = []): array;
}
