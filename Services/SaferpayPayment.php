<?php

namespace Payment\Bundle\SaferpayBundle\Services;

use Payment\Bundle\SaferpayBundle\Controller\SaferpayController;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SaferpayPayment extends SaferpayController
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    protected function getContainer()
    {
        return $this->container;
    }
}