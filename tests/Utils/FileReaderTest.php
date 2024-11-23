<?php

namespace Tests\Utils;

use App\Utils\FileReader;
use PHPUnit\Framework\TestCase;

/**
 * Testes para a classe FileReader.
 */
class FileReaderTest extends TestCase
{
    /**
     * Caminho temporário para testes com arquivos.
     */
    private string $tempFile;

    protected function setUp(): void
    {
        parent::setUp();

        // Cria um arquivo temporário para os testes.
        $this->tempFile = sys_get_temp_dir() . '/test_file.txt';
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Remove o arquivo temporário após os testes.
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }

    /**
     * Testa o método exists para um arquivo existente.
     */
    public function testExistsReturnsTrueForExistingFile(): void
    {
        // Cria o arquivo temporário.
        file_put_contents($this->tempFile, "Test content");

        $fileReader = new FileReader();
        $this->assertTrue($fileReader->exists($this->tempFile));
    }

    /**
     * Testa o método exists para um arquivo inexistente.
     */
    public function testExistsReturnsFalseForNonExistingFile(): void
    {
        $fileReader = new FileReader();
        $this->assertFalse($fileReader->exists('/non/existing/file.txt'));
    }

    /**
     * Testa o método readLines para um arquivo com conteúdo.
     */
    public function testReadLinesReturnsArrayOfLines(): void
    {
        // Cria o arquivo temporário com múltiplas linhas.
        $content = "Line 1\nLine 2\n\nLine 3";
        file_put_contents($this->tempFile, $content);

        $fileReader = new FileReader();
        $lines = $fileReader->readLines($this->tempFile);

        $this->assertIsArray($lines);
        $this->assertCount(3, $lines); // Ignora a linha vazia.
        $this->assertEquals(['Line 1', 'Line 2', 'Line 3'], $lines);
    }

    /**
     * Testa o método readLines para um arquivo vazio.
     */
    public function testReadLinesReturnsEmptyArrayForEmptyFile(): void
    {
        // Cria um arquivo vazio.
        file_put_contents($this->tempFile, "");

        $fileReader = new FileReader();
        $lines = $fileReader->readLines($this->tempFile);

        $this->assertIsArray($lines);
        $this->assertEmpty($lines);
    }

}
