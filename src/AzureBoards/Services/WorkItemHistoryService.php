<?php

namespace App\AzureBoards\Services;

use App\AzureBoards\Contracts\ApiServiceInterface;
use App\AzureBoards\Contracts\AzureBoardsClientInterface;

/**
 * Classe WorkItemHistoryService
 *
 * Serviço responsável por recuperar o histórico de atualizações de um Work Item na API do Azure Boards.
 * Este serviço encapsula a lógica necessária para interagir com o endpoint de histórico de Work Items.
 *
 * @package App\AzureBoards\Services
 */
class WorkItemHistoryService implements ApiServiceInterface
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
     * Construtor da classe WorkItemHistoryService.
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
     * Executa uma requisição para obter o histórico de atualizações de um Work Item.
     *
     * @param array $params Parâmetros necessários para a requisição. Deve incluir:
     *  - `id` (int): O ID do Work Item cujo histórico será recuperado.
     *
     * @return array Retorna o histórico de atualizações como um array JSON decodificado.
     */
    public function execute(array $params): array
    {
        $id = $params['id'];
        $url = "https://dev.azure.com/{$this->organization}/_apis/wit/workitems/{$id}/updates?api-version=7.0";

        return $this->client->request('GET', $url);
    }
}
