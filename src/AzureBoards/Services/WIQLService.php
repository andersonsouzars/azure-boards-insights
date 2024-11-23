<?php

namespace App\AzureBoards\Services;

use App\AzureBoards\Abstract\AbstractApiService;
use App\AzureBoards\Contracts\AzureBoardsClientInterface;

/**
 * Classe WIQLService
 *
 * Serviço responsável por executar consultas WIQL (Work Item Query Language) na API do Azure Boards.
 * Este serviço encapsula a lógica necessária para interagir com o endpoint de consultas WIQL.
 *
 * @package App\AzureBoards\Services
 */
class WIQLService extends AbstractApiService
{
    /**
     * @var AzureBoardsClientInterface Cliente para realizar requisições HTTP autenticadas à API do Azure Boards.
     */
    private AzureBoardsClientInterface $client;

    /**
     * Construtor da classe WIQLService.
     *
     * @param AzureBoardsClientInterface $client Instância do cliente para comunicação com a API do Azure Boards.
     * @param string $organization Nome da organização no Azure DevOps.
     * @param string $project Nome do projeto no Azure DevOps.
     */
    public function __construct(AzureBoardsClientInterface $client)
    {
        $this->client = $client;

        $organization = $_ENV['AZURE_DEVOPS_ORGANIZATION'];
        $project = $_ENV['AZURE_DEVOPS_PROJECT'];
        $this->url = "https://dev.azure.com/{$organization}/{$project}/_apis/wit/wiql?api-version=7.0";
    }

    /**
     * Executa uma consulta WIQL na API do Azure Boards.
     *
     * @param array $params Parâmetros necessários para a consulta WIQL. Deve incluir a estrutura da query.
     *
     * @return array Retorna os resultados da consulta WIQL como um array JSON decodificado.
     */
    public function execute(array $params): array
    {
        return $this->client->request('POST', $this->url, ['json' => $params]);
    }
}
