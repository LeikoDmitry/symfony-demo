<?php

declare(strict_types=1);

namespace SymfonyDemo\Factory;

use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use SymfonyDemo\Controller\SecurityController;

/**
 * Class SecurityControllerFactory
 *
 * @package SymfonyDemo\Factory
 */
class SecurityControllerFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ) {
        return new SecurityController($container->get(AuthenticationService::class));
    }
}
