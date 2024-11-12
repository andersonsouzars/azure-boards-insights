<?php

namespace Tests\AzureBoards\Services;

use App\AzureBoards\Contracts\AzureBoardsClientInterface;
use App\AzureBoards\Services\WIQLService;
use PHPUnit\Framework\TestCase;

/**
 * Classe de Testes para WIQLService
 *
 * Testa o comportamento do serviço WIQLService, responsável por executar consultas WIQL na API do Azure Boards.
 * Utiliza mocks para simular interações com o cliente da API.
 */
class WIQLServiceTest extends TestCase
{
    /**
     * Testa se o método `execute` retorna os dados esperados.
     *
     * - Simula um cliente da API que retorna uma lista de Work Items.
     * - Verifica se a resposta contém a chave `workItems` e se há dois itens na lista.
     *
     * @return void
     */
    public function testExecuteReturnsExpectedData()
    {
        // Mock do cliente da API
        $mockClient = $this->createMock(AzureBoardsClientInterface::class);
        $mockClient->method('request')
                   ->willReturn(['workItems' => [['id' => 1], ['id' => 2]]]);

        // Instancia o serviço com o mock
        $service = new WIQLService($mockClient, 'org', 'project');

        // Executa o método do serviço
        $response = $service->execute(['query' => 'WIQL Query']);

        // Verifica se a resposta possui os dados esperados
        $this->assertArrayHasKey('workItems', $response, 'A resposta deve conter a chave "workItems".');
        $this->assertCount(2, $response['workItems'], 'A lista de Work Items deve conter 2 itens.');
    }
}
