<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use EonX\EasyWebhook\Bridge\BridgeConstantsInterface;
use EonX\EasyWebhook\Formatters\JsonFormatter;
use EonX\EasyWebhook\HttpClientFactory;
use EonX\EasyWebhook\Interfaces\HttpClientFactoryInterface;
use EonX\EasyWebhook\Interfaces\WebhookBodyFormatterInterface;
use EonX\EasyWebhook\Interfaces\WebhookClientInterface;
use EonX\EasyWebhook\Interfaces\WebhookResultHandlerInterface;
use EonX\EasyWebhook\Interfaces\WebhookResultStoreInterface;
use EonX\EasyWebhook\Interfaces\WebhookRetryStrategyInterface;
use EonX\EasyWebhook\RetryStrategies\MultiplierWebhookRetryStrategy;
use EonX\EasyWebhook\Stores\NullWebhookResultStore;
use EonX\EasyWebhook\WebhookClient;
use EonX\EasyWebhook\WebhookResultHandler;
use EonX\EasyWebhook\WithEventsWebhookClient;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Contracts\HttpClient\HttpClientInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure();

    // Body Formatter (Default)
    $services->set(WebhookBodyFormatterInterface::class, JsonFormatter::class);

    // HTTP Client
    $services
        // Factory
        ->set(HttpClientFactoryInterface::class, HttpClientFactory::class)
        // Client
        ->set(BridgeConstantsInterface::HTTP_CLIENT, HttpClientInterface::class)
        ->factory([ref(HttpClientFactoryInterface::class), 'create']);

    // Webhook Retry Strategy (Default)
    $services->set(WebhookRetryStrategyInterface::class, MultiplierWebhookRetryStrategy::class);

    // Webhook Result Handler
    $services->set(WebhookResultHandlerInterface::class, WebhookResultHandler::class);

    // Webhook Client
    $services
        ->set(WebhookClientInterface::class, WebhookClient::class)
        ->arg('$configurators', tagged_polyfill(BridgeConstantsInterface::TAG_WEBHOOK_CONFIGURATOR))
        ->arg('$httpClient', ref(BridgeConstantsInterface::HTTP_CLIENT));

    // Webhook Client With Events
    $services
        ->set(WithEventsWebhookClient::class)
        ->decorate(WebhookClientInterface::class, null, 1);

    // Webhook Store (Default)
    $services->set(WebhookResultStoreInterface::class, NullWebhookResultStore::class);
};

function tagged_polyfill(string $tag, string $indexAttribute = null, string $defaultIndexMethod = null): TaggedIteratorArgument
{
    // works in Symfony 4.*
    if (function_exists('Symfony\Component\DependencyInjection\Loader\Configurator\tagged')) {
        return tagged($tag, $indexAttribute, $defaultIndexMethod);
    }

    // works in Symfony 4.4+ and 5
    return tagged_iterator($tag, $indexAttribute, $defaultIndexMethod);
}
