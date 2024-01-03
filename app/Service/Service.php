<?php

declare(strict_types=1);

namespace App\Service;

use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;

abstract class Service
{
    protected ContainerInterface $container;

    /**
     * @var LoggerFactory
     */
    protected \Psr\Log\LoggerInterface|LoggerFactory $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->logger = $container->get(LoggerFactory::class)->get('service');
    }
}