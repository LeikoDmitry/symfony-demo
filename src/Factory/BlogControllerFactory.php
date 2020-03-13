<?php

declare(strict_types=1);

namespace SymfonyDemo\Factory;

use Doctrine\ORM\EntityManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use SymfonyDemo\Controller\BlogController;

/**
 * Class BlogControllerFactory
 *
 * @package SymfonyDemo\Factory
 */
class BlogControllerFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface  $container
     * @param  string  $requestedName
     * @param  array|null  $options
     *
     * @return BlogController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new BlogController($container->get(EntityManager::class));
    }
}
