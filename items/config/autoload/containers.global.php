<?php

return [
    // Provides application-wide services.
    // We recommend using fully-qualified class names whenever possible as
    // service names.
    'dependencies' => [
        // Use 'aliases' to alias a service name to another service. The
        // key is the alias name, the value is the service to which it points.
        'aliases' => [
            // Fully\Qualified\ClassOrInterfaceName::class => Fully\Qualified\ClassName::class,
        ],
        // Use 'invokables' for constructor-less services, or services that do
        // not require arguments to the constructor. Map a service name to the
        // class name.
        'invokables' => [
        ],
        // Use 'factories' for services provided by callbacks/factory classes.
        'factories'  => [
            App\Service\ItemService::class => App\Service\ItemServiceFactory::class,
            App\Handler\Item::class => App\Handler\ItemFactory::class,
            App\Handler\HealthCheck::class => App\Handler\HealthCheckFactory::class,
        ],
    ],
];
