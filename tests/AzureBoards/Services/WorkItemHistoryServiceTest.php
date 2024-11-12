<?php

namespace Tests\AzureBoards\Services;

use App\AzureBoards\Contracts\AzureBoardsClientInterface;
use App\AzureBoards\Services\WorkItemHistoryService;
use PHPUnit\Framework\TestCase;

/**
 * Classe de Testes para WorkItemHistoryService
 *
 * Testa o comportamento do serviço WorkItemHistoryService, responsável por recuperar
 * o histórico de atualizações de um Work Item na API do Azure Boards.
 * Utiliza mocks para simular interações com o cliente da API.
 */
class WorkItemHistoryServiceTest extends TestCase
{
    /**
     * Testa se o método `execute` retorna o histórico esperado de um Work Item.
     *
     * - Simula um cliente da API que retorna o histórico de atualizações de um Work Item.
     * - Verifica se a resposta contém a chave `value` e se o histórico possui o ID correto.
     *
     * @return void
     */
    public function testExecuteReturnsWorkItemHistory()
    {
        // Mock do cliente da API
        $mockClient = $this->createMock(AzureBoardsClientInterface::class);
        $mockClient->method('request')
                   ->willReturn(['value' => [['id' => 1, 'fields' => ['System.State' => 'Done']]]]);

        // Instancia o serviço com o mock
        $service = new WorkItemHistoryService($mockClient, 'organization');

        // Executa o método do serviço
        $response = $service->execute(['id' => 123]);

        // Verifica se a resposta possui os dados esperados
        $this->assertArrayHasKey('value', $response, 'A resposta deve conter a chave "value".');
        $this->assertCount(1, $response['value'], 'O histórico deve conter exatamente 1 item.');
        $this->assertEquals(1, $response['value'][0]['id'], 'O ID do Work Item deve ser 1.');
    }

    /**
     * Testa se o método `execute` lança uma exceção em caso de erro do cliente.
     *
     * - Simula um cliente da API que lança uma RuntimeException.
     * - Verifica se o método `execute` do serviço também lança uma RuntimeException.
     *
     * @return void
     */
    public function testExecuteThrowsExceptionOnClientError()
    {
        // Mock do cliente da API
        $mockClient = $this->createMock(AzureBoardsClientInterface::class);
        $mockClient->method('request')
                   ->willThrowException(new \RuntimeException('Request failed'));

        // Instancia o serviço com o mock
        $service = new WorkItemHistoryService($mockClient, 'organization');

        // Espera que uma exceção seja lançada
        $this->expectException(\RuntimeException::class);

        // Executa o método que deve falhar
        $service->execute(['id' => 123]);
    }
}
