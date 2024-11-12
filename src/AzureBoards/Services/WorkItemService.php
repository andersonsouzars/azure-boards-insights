<?php

namespace App\AzureBoards\Services;

use App\AzureBoards\Contracts\ApiServiceInterface;
use App\AzureBoards\Contracts\AzureBoardsClientInterface;

/**
 * Classe WorkItemService
 *
 * Serviço responsável por recuperar os detalhes de Work Items na API do Azure Boards.
 * Este serviço encapsula a lógica necessária para interagir com o endpoint de consulta de Work Items.
 *
 * @package App\AzureBoards\Services
 */
class WorkItemService implements ApiServiceInterface
{
    /**
     * @var AzureBoardsClientInterface Cliente para realizar requisições HTTP autenticadas à API do Azure Boards.
     */
    private AzureBoardsClientInterface $client;

    /**
     * @var string Nome da organização no Azure DevOps.
     */
    private string $organization;

    /**
     * Construtor da classe WorkItemService.
     *
     * @param AzureBoardsClientInterface $client Instância do cliente para comunicação com a API do Azure Boards.
     * @param string $organization Nome da organização no Azure DevOps.
     */
    public function __construct(AzureBoardsClientInterface $client, string $organization)
    {
        $this->client = $client;
        $this->organization = $organization;
    }

    /**
     * Executa uma requisição para obter os detalhes de Work Items.
     *
     * @param array $params Parâmetros necessários para a requisição. Deve incluir:
     *  - `ids` (array): IDs dos Work Items a serem recuperados.
     *  - `fields` (string, opcional): Lista de campos a serem retornados. Por padrão, retorna `System.State,System.ChangedDate`.
     *
     * @return array Retorna os detalhes dos Work Items como um array JSON decodificado.
     */
    public function execute(array $params): array
    {
        $ids = implode(",", $params['ids']);
        $fields = $params['fields'] ?? 'System.State,System.ChangedDate';
        $url = "https://dev.azure.com/{$this->organization}/_apis/wit/workitems?ids={$ids}&fields={$fields}&api-version=7.0";

        return $this->client->request('GET', $url);
    }
}
