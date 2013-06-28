<?php

namespace Payment\Bundle\SaferpayBundle\Tests\Http;

use Payment\Bundle\SaferpayBundle\Http\HttpClientFactory;
use Payment\HttpClient\HttpClientInterface;

class HttpClientFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return HttpClientInterface
     */
    public function testCreateClient()
    {
        $httpClientInterface = 'Payment\HttpClient\HttpClientInterface';
        $client = $this->getMock($httpClientInterface);
        $factory = new HttpClientFactory($client);
        $this->assertInstanceOf($httpClientInterface, $factory->createClient());
    }
}