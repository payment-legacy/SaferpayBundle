<?php

namespace Payment\Bundle\SaferpayBundle\Http;

use Payment\HttpClient\HttpClientInterface;

class HttpClientFactory
{
    /**
     * @var HttpClientInterface
     */
    protected $client;

    /**
     * @param HttpClientInterface $client
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @return HttpClientInterface
     */
    public function getClient()
    {
        return clone $this->client;
    }
}