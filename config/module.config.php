<?php

declare(strict_types=1);

namespace SymfonyDemo;

use Doctrine\ORM\EntityManager;
use Laminas\Authentication\AuthenticationService;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\DBAL\Driver\PDOMySql\Driver;
use SymfonyDemo\Entity\User;
use SymfonyDemo\Factory\AuthenticationServiceFactory;
use SymfonyDemo\Factory\BlogControllerFactory;
use SymfonyDemo\Factory\SecurityControllerFactory;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'blog' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/blog',
                    'defaults' => [
                        'controller' => Controller\BlogController::class,
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'detail' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/[:slug]',
                            'defaults' => [
                                'action' => 'postShow',
                            ],
                            'constraints' => [
                                'slug' => '[a-z][a-z0-9_-]*',
                            ],
                        ],
                    ],
                    'search' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/search',
                            'defaults' => [
                                'action' => 'search',
                            ],
                        ],
                    ],
                ],
            ],
            'login' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/login',
                    'defaults' => [
                        'controller' => Controller\SecurityController::class,
                        'action'     => 'login',
                    ],
                ],
            ],
            'logout' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/logout',
                    'defaults' => [
                        'controller' => Controller\SecurityController::class,
                        'action'     => 'logout',
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'aliases' => [

        ],
        'factories' => [
            AuthenticationService::class => AuthenticationServiceFactory::class
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            Controller\SecurityController::class => SecurityControllerFactory::class,
            Controller\BlogController::class => BlogControllerFactory::class
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    'doctrine' => [
        'driver' => [
            'symfony_demo_annotation_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Entity',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    'SymfonyDemo' => 'symfony_demo_annotation_driver',
                ],
            ],
        ],
        'connection' => [
            'orm_default' => [
                'driverClass' => Driver::class,
                'params' => [
                    'host'     => 'db',
                    'port'     => 3306,
                    'user'     => getenv('USERNAME'),
                    'password' => getenv('PASSWORD'),
                    'dbname'   => getenv('DATABASE'),
                ],
            ],
        ],
        'authentication' => [
            'orm_default' => [
                'object_manager' => EntityManager::class,
                'identity_class' => User::class,
                'identity_property' => 'username',
                'credential_property' => 'password',
                'credential_callable' => sprintf(
                    '%s%s',
                    __NAMESPACE__,
                    '\Entity\User::verifyCredential'
                )
            ],
        ],
    ],
];
