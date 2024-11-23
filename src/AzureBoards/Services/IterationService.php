<?php

namespace App\AzureBoards\Services;

use App\AzureBoards\Abstract\AbstractApiService;
use App\AzureBoards\Contracts\AzureBoardsClientInterface;

/**
 * Classe IterationService
 *
 * Serviço responsável por recuperar os iterations de uma Squad na API do Azure Boards.
 * Este serviço encapsula a lógica necessária para interagir com o endpoint de consulta de Iterations.
 *
 * @package App\AzureBoards\Services
 */
class IterationService extends AbstractApiService
{
    /**
     * @var AzureBoardsClientInterface Cliente para realizar requisições HTTP autenticadas à API do Azure Boards.
     */
    private AzureBoardsClientInterface $client;

    /**
     * Construtor da classe IterationService.
     *
     * @param AzureBoardsClientInterface $client Instância do cliente para comunicação com a API do Azure Boards.
     */
    public function __construct(AzureBoardsClientInterface $client)
    {
        $organization = $_ENV['AZURE_DEVOPS_ORGANIZATION'];
        $project = $_ENV['AZURE_DEVOPS_PROJECT'];
        $team = rawurlencode($_ENV['AZURE_DEVOPS_TEAM']);

        $this->client = $client;
        $this->url = "https://dev.azure.com/$organization/$project/$team/_apis/work/teamsettings/iterations?api-version=7.0";
        
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
        return $this->client->request('GET', $this->url);
    }
}
