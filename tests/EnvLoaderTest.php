<?php

declare(strict_types=1);

namespace Tests\Utils;

use PHPUnit\Framework\TestCase;
use App\Utils\EnvLoader;
use App\Utils\FileReaderInterface;
use App\Exceptions\EnvLoaderException;

class EnvLoaderTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&\App\Utils\FileReaderInterface
     */
    private FileReaderInterface $fileReaderMock;

    protected function setUp(): void
    {
        // Criar mock da interface FileReaderInterface
        $this->fileReaderMock = $this->createMock(FileReaderInterface::class);
    }

    public function testLoadValidEnv(): void
    {
        // Configurar mock para simular um arquivo válido
        $this->fileReaderMock->method('exists')->willReturn(true);
        $this->fileReaderMock->method('readLines')->willReturn([
            'APP_NAME=TestApp',
            'APP_ENV=test',
            'BASE_URL=http://mock.local',
            'API_URL={BASE_URL}/api'
        ]);

        $envLoader = new EnvLoader('/mock/path/.env', $this->fileReaderMock);
        $envLoader->load();

        $this->assertEquals('TestApp', EnvLoader::get('APP_NAME'));
        $this->assertEquals('test', EnvLoader::get('APP_ENV'));
        $this->assertEquals('http://mock.local', EnvLoader::get('BASE_URL'));
        $this->assertEquals('http://mock.local/api', EnvLoader::get('API_URL'));
    }

    public function testThrowsExceptionWhenFileDoesNotExist(): void
    {
        // Configurar mock para simular arquivo inexistente
        $this->fileReaderMock->method('exists')->willReturn(false);

        $this->expectException(EnvLoaderException::class);
        $this->expectExceptionMessage('Arquivo .env não encontrado no caminho especificado: /mock/path/.env');

        new EnvLoader('/mock/path/.env', $this->fileReaderMock);
    }

    public function testThrowsExceptionForInvalidEnvLine(): void
    {
        // Configurar mock para simular um arquivo com linhas inválidas
        $this->fileReaderMock->method('exists')->willReturn(true);
        $this->fileReaderMock->method('readLines')->willReturn([
            'INVALID_LINE'
        ]);

        $envLoader = new EnvLoader('/mock/path/.env', $this->fileReaderMock);

        $this->expectException(EnvLoaderException::class);
        $this->expectExceptionMessage('Formato inválido no arquivo .env: INVALID_LINE');

        $envLoader->load();
    }

    public function testEnvironmentVariablesSetProperly(): void
    {
        // Configurar mock para simular variáveis válidas
        $this->fileReaderMock->method('exists')->willReturn(true);
        $this->fileReaderMock->method('readLines')->willReturn([
            'APP_NAME=TestApp',
            'APP_ENV=test'
        ]);

        $envLoader = new EnvLoader('/mock/path/.env', $this->fileReaderMock);
        $envLoader->load();

        $this->assertEquals('TestApp', getenv('APP_NAME'));
        $this->assertEquals('test', getenv('APP_ENV'));
    }

    public function testResolveReferences(): void
    {
        // Configurar mock para simular referências no arquivo
        $this->fileReaderMock->method('exists')->willReturn(true);
        $this->fileReaderMock->method('readLines')->willReturn([
            'APP_NAME=TestApp',
            'APP_ENV=test',
            'GREETING={APP_NAME} is running in {APP_ENV} mode'
        ]);

        $envLoader = new EnvLoader('/mock/path/.env', $this->fileReaderMock);
        $envLoader->load();

        $this->assertEquals('TestApp is running in test mode', EnvLoader::get('GREETING'));
    }

    public function testGetReturnsDefaultForUndefinedVariables(): void
    {
        // Configurar mock para simular variáveis
        $this->fileReaderMock->method('exists')->willReturn(true);
        $this->fileReaderMock->method('readLines')->willReturn([
            'APP_NAME=TestApp'
        ]);

        $envLoader = new EnvLoader('/mock/path/.env', $this->fileReaderMock);
        $envLoader->load();

        $this->assertEquals('default_value', EnvLoader::get('UNDEFINED_VAR', 'default_value'));
    }

    public function testProcessEnvLineIgnoresEmptyLines(): void
    {
        $this->fileReaderMock->method('exists')->willReturn(true);
        $this->fileReaderMock->method('readLines')->willReturn(["", "   ", "\n"]);

        $envLoader = new EnvLoader('/mock/path/.env', $this->fileReaderMock);
        $envLoader->load();

        // Verifica se nenhuma variável inesperada foi definida
        $this->assertArrayNotHasKey('', $_ENV);
    }

    public function testSanitizeValueRemovesQuotes(): void
    {
        $reflection = new \ReflectionClass(EnvLoader::class);
        $method = $reflection->getMethod('sanitizeValue');
        $method->setAccessible(true);

        $this->assertEquals('value', $method->invokeArgs(null, ['"value"']));
        $this->assertEquals('value', $method->invokeArgs(null, ["'value'"]));
    }

    public function testSanitizeValueReturnsUnchangedValue(): void
    {
        $reflection = new \ReflectionClass(EnvLoader::class);
        $method = $reflection->getMethod('sanitizeValue');
        $method->setAccessible(true);

        $this->assertEquals('value', $method->invokeArgs(null, ['value']));
    }
}
