<?php

namespace Tests\AzureBoards\Services;

use App\AzureBoards\Contracts\AzureBoardsClientInterface;
use App\AzureBoards\Services\WorkItemService;
use PHPUnit\Framework\TestCase;

/**
 * Classe de Testes para WorkItemService
 *
 * Testa o comportamento do serviço WorkItemService, responsável por recuperar
 * os detalhes de Work Items na API do Azure Boards.
 * Utiliza mocks para simular interações com o cliente da API.
 */
class WorkItemServiceTest extends TestCase
{
    /**
     * Testa se o método `execute` retorna os detalhes esperados dos Work Items.
     *
     * - Simula um cliente da API que retorna uma lista de Work Items.
     * - Verifica se a resposta contém a chave `value`, se o número de itens está correto
     *   e se os detalhes retornados estão conforme esperado.
     *
     * @return void
     */
    public function testExecuteReturnsWorkItems()
    {
        // Mock do cliente da API
        $mockClient = $this->createMock(AzureBoardsClientInterface::class);
        $mockClient->method('request')
                   ->willReturn(['value' => [['id' => 1, 'fields' => ['System.State' => 'Active']]]]);

        // Instancia o serviço com o mock
        $service = new WorkItemService($mockClient, 'organization');

        // Executa o método do serviço
        $response = $service->execute(['ids' => [1, 2, 3], 'fields' => 'System.State']);

        // Verifica se a resposta possui os dados esperados
        $this->assertArrayHasKey('value', $response, 'A resposta deve conter a chave "value".');
        $this->assertCount(1, $response['value'], 'A lista de Work Items deve conter exatamente 1 item.');
        $this->assertEquals(1, $response['value'][0]['id'], 'O ID do Work Item deve ser 1.');
        $this->assertEquals('Active', $response['value'][0]['fields']['System.State'], 'O estado do Work Item deve ser "Active".');
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
        $service = new WorkItemService($mockClient, 'organization');

        // Espera que uma exceção seja lançada
        $this->expectException(\RuntimeException::class);

        // Executa o método que deve falhar
        $service->execute(['ids' => [1, 2, 3]]);
    }
}
