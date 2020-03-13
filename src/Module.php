<?php

declare(strict_types=1);

namespace SymfonyDemo;

use Laminas\ServiceManager\Factory\InvokableFactory;
use SymfonyDemo\View\Helper\MarkDown;
use Laminas\ModuleManager\Feature\ViewHelperProviderInterface;

class Module implements ViewHelperProviderInterface
{
    /**
     * @return array
     */
    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * @return array
     */
    public function getViewHelperConfig(): array
    {
        return [
            'aliases' => [
                'markdown' => MarkDown::class,
            ],
            'factories' => [
                MarkDown::class => InvokableFactory::class,
            ],
        ];
    }
}
