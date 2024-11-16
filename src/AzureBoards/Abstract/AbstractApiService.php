<?php

namespace App\AzureBoards\Abstract;

use App\AzureBoards\Contracts\ApiServiceInterface;

abstract class AbstractApiService implements ApiServiceInterface
{
    protected string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
