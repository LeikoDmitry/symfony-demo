<?php

declare(strict_types=1);

namespace SymfonyDemo\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AuthenticationServiceFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface  $container
     * @param  string  $requestedName
     * @param  array|null  $options
     *
     * @return mixed|object
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ) {
        return $container->get('doctrine.authenticationservice.orm_default');
    }
}
