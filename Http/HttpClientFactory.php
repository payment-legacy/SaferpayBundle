<?php

namespace Payment\Bundle\SaferpayBundle\Http;

use Payment\HttpClient\HttpClientInterface;

class HttpClientFactory
{
    /**
     * @var HttpClientInterface
     */
    protected $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param bool $clone
     * @return HttpClientInterface
     */
    public function getClient($clone = true)
    {
        if(true === $clone){
            return clone $this->client;
        }
        return $this->client;
    }
}