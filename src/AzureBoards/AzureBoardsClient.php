<?php

namespace App\AzureBoards;

use App\AzureBoards\Contracts\AzureBoardsClientInterface;
use GuzzleHttp\Client;

/**
 * Classe AzureBoardsClient
 *
 * Esta classe fornece um cliente para interagir com a API do Azure Boards.
 * Utiliza o GuzzleHTTP para realizar requisições HTTP e gerencia a autenticação via Personal Access Token (PAT).
 *
 * @package App\AzureBoards
 */
class AzureBoardsClient implements AzureBoardsClientInterface
{
    /**
     * @var Client Cliente HTTP Guzzle utilizado para realizar as requisições.
     */
    private Client $httpClient;

    /**
     * @var string Token de Acesso Pessoal (PAT) para autenticação na API do Azure Boards.
     */
    private string $pat;

    /**
     * Construtor da classe AzureBoardsClient.
     *
     * @param string $pat Token de Acesso Pessoal (PAT) para autenticação na API do Azure Boards.
     * @param Client|null $httpClient Instância opcional do cliente GuzzleHTTP. Caso não seja fornecida, uma nova instância será criada.
     */
    public function __construct(string $pat, ?Client $httpClient = null)
    {
        $this->httpClient = $httpClient ?: new Client();
        $this->pat = $pat;
    }

    /**
     * Faz uma requisição para a API do Azure Boards.
     *
     * @param string $method Método HTTP a ser utilizado (ex.: "GET", "POST").
     * @param string $url URL completa do endpoint da API do Azure Boards.
     * @param array $options Parâmetros opcionais para a requisição (ex.: query, body, headers).
     *
     * @return array Retorna a resposta da API como um array JSON decodificado.
     * 
     * @throws \RuntimeException Caso a requisição falhe ou ocorra um erro.
     */
    public function request(string $method, string $url, array $options = []): array
    {
        $headers = [
            'Authorization' => 'Basic ' . base64_encode(":{$this->pat}"),
            'Content-Type' => 'application/json',
        ];

        $options['headers'] = $headers;

        try {
            $response = $this->httpClient->request($method, $url, $options);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            throw new \RuntimeException("Falha na requisição à API do Azure Boards: " . $e->getMessage());
        }
    }
}
