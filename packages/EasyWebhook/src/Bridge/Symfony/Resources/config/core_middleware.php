<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use EonX\EasyWebhook\Bridge\BridgeConstantsInterface;
use EonX\EasyWebhook\Interfaces\MiddlewareInterface;
use EonX\EasyWebhook\Middleware\AsyncMiddleware;
use EonX\EasyWebhook\Middleware\EventsMiddleware;
use EonX\EasyWebhook\Middleware\LockMiddleware;
use EonX\EasyWebhook\Middleware\MethodMiddleware;
use EonX\EasyWebhook\Middleware\ResetStoreMiddleware;
use EonX\EasyWebhook\Middleware\SendWebhookMiddleware;
use EonX\EasyWebhook\Middleware\StatusAndAttemptMiddleware;
use EonX\EasyWebhook\Middleware\StoreMiddleware;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure();

    // BEFORE MIDDLEWARE
    $services
        ->set(LockMiddleware::class)
        ->arg('$priority', MiddlewareInterface::PRIORITY_CORE_BEFORE);

    // AFTER MIDDLEWARE
    $services
        ->set(ResetStoreMiddleware::class)
        ->arg('$priority', MiddlewareInterface::PRIORITY_CORE_AFTER);

    $services
        ->set(MethodMiddleware::class)
        ->arg('$method', '%' . BridgeConstantsInterface::PARAM_METHOD . '%')
        ->arg('$priority', MiddlewareInterface::PRIORITY_CORE_AFTER + 10);

    $services
        ->set(AsyncMiddleware::class)
        ->arg('$enabled', '%' . BridgeConstantsInterface::PARAM_ASYNC . '%')
        ->arg('$priority', MiddlewareInterface::PRIORITY_CORE_AFTER + 20);

    $services
        ->set(StoreMiddleware::class)
        ->arg('$priority', MiddlewareInterface::PRIORITY_CORE_AFTER + 30);

    $services
        ->set(EventsMiddleware::class)
        ->arg('$priority', MiddlewareInterface::PRIORITY_CORE_AFTER + 40);

    $services
        ->set(StatusAndAttemptMiddleware::class)
        ->arg('$priority', MiddlewareInterface::PRIORITY_CORE_AFTER + 50);

    // Make sure SendWebhookMiddleware is always last
    $services
        ->set(SendWebhookMiddleware::class)
        ->arg('$httpClient', ref(BridgeConstantsInterface::HTTP_CLIENT))
        ->arg('$priority', MiddlewareInterface::PRIORITY_CORE_AFTER + 60);
};
