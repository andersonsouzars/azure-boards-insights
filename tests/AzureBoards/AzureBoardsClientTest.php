<?php

namespace Tests\AzureBoards;

use App\AzureBoards\AzureBoardsClient;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * Classe de Testes para AzureBoardsClient
 *
 * Esta classe testa o comportamento do cliente AzureBoardsClient ao realizar requisições HTTP.
 * Utiliza mocks para simular interações com o GuzzleHTTP Client e verificar o comportamento esperado.
 */
class AzureBoardsClientTest extends TestCase
{
    /**
     * Testa se o método `request` retorna a resposta esperada.
     *
     * - Simula um cliente GuzzleHTTP retornando um JSON válido.
     * - Verifica se o método `request` retorna um array decodificado com o valor esperado.
     *
     * @return void
     */
    public function testRequestReturnsExpectedResponse()
    {
        $mockResponse = new Response(200, [], json_encode(['key' => 'value']));

        // Mock do Guzzle Client
        $mockHttpClient = $this->createMock(Client::class);
        $mockHttpClient->method('request')
                       ->willReturn($mockResponse);

        // Simula a injeção do cliente no AzureBoardsClient
        $azureClient = new AzureBoardsClient('fake-pat', $mockHttpClient);

        $response = $azureClient->request('GET', 'https://example.com');

        $this->assertIsArray($response, 'A resposta deve ser um array.');
        $this->assertEquals('value', $response['key'], 'O valor da chave "key" deve ser "value".');
    }

    /**
     * Testa se o método `request` lança uma exceção ao ocorrer um erro.
     *
     * - Simula um cliente GuzzleHTTP lançando uma exceção.
     * - Verifica se o método `request` lança uma RuntimeException com a mensagem esperada.
     *
     * @return void
     */
    public function testRequestThrowsExceptionOnError()
    {
        // Mock do Guzzle Client
        $mockHttpClient = $this->createMock(Client::class);
        $mockHttpClient->method('request')
                       ->willThrowException(new \Exception('Request failed'));

        // Simula a injeção do cliente no AzureBoardsClient
        $azureClient = new AzureBoardsClient('fake-pat', $mockHttpClient);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Falha na requisição à API do Azure Boards: Request failed');

        $azureClient->request('GET', 'https://example.com');
    }
}
