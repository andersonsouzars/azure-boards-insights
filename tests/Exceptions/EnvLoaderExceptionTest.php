<?php

namespace Tests\Exceptions;

use App\Exceptions\EnvLoaderException;
use PHPUnit\Framework\TestCase;

/**
 * Testes para a classe EnvLoaderException.
 */
class EnvLoaderExceptionTest extends TestCase
{
    /**
     * Testa se a exceção pode ser instanciada.
     */
    public function testEnvLoaderExceptionCanBeInstantiated(): void
    {
        $exception = new EnvLoaderException();
        $this->assertInstanceOf(EnvLoaderException::class, $exception);
    }

    /**
     * Testa se a exceção estende RuntimeException.
     */
    public function testEnvLoaderExceptionExtendsRuntimeException(): void
    {
        $exception = new EnvLoaderException();
        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    /**
     * Testa se a mensagem personalizada é atribuída corretamente.
     */
    public function testEnvLoaderExceptionCustomMessage(): void
    {
        $message = "Custom error message";
        $exception = new EnvLoaderException($message);

        $this->assertEquals($message, $exception->getMessage());
    }

    /**
     * Testa se o código personalizado é atribuído corretamente.
     */
    public function testEnvLoaderExceptionCustomCode(): void
    {
        $code = 123;
        $exception = new EnvLoaderException("Error message", $code);

        $this->assertEquals($code, $exception->getCode());
    }

    /**
     * Testa se uma exceção pode ser encadeada corretamente.
     */
    public function testEnvLoaderExceptionWithPreviousException(): void
    {
        $previousException = new \Exception("Previous exception");
        $exception = new EnvLoaderException("Error message", 0, $previousException);

        $this->assertSame($previousException, $exception->getPrevious());
    }

    public function testCustomErrorMessage(): void
    {
        $exception = new EnvLoaderException("Test message");
        $this->assertEquals("EnvLoaderException: Test message", $exception->customErrorMessage());
    }
}
