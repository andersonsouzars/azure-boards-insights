<?php

namespace App\AzureBoards\Services;

use App\AzureBoards\Contracts\ApiServiceInterface;
use App\AzureBoards\Contracts\AzureBoardsClientInterface;

/**
 * Classe WIQLService
 *
 * Serviço responsável por executar consultas WIQL (Work Item Query Language) na API do Azure Boards.
 * Este serviço encapsula a lógica necessária para interagir com o endpoint de consultas WIQL.
 *
 * @package App\AzureBoards\Services
 */
class WIQLService implements ApiServiceInterface
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
     * @var string Nome do projeto no Azure DevOps.
     */
    private string $project;

    /**
     * Construtor da classe WIQLService.
     *
     * @param AzureBoardsClientInterface $client Instância do cliente para comunicação com a API do Azure Boards.
     * @param string $organization Nome da organização no Azure DevOps.
     * @param string $project Nome do projeto no Azure DevOps.
     */
    public function __construct(AzureBoardsClientInterface $client, string $organization, string $project)
    {
        $this->client = $client;
        $this->organization = $organization;
        $this->project = $project;
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
        $url = "https://dev.azure.com/{$this->organization}/{$this->project}/_apis/wit/wiql?api-version=7.0";
        return $this->client->request('POST', $url, ['json' => $params]);
    }
}
