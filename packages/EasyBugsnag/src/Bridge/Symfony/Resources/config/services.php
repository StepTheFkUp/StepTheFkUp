<?php

declare(strict_types=1);

use Bugsnag\Client;
use EonX\EasyBugsnag\Bridge\BridgeConstantsInterface;
use EonX\EasyBugsnag\Bridge\Symfony\Shutdown\ShutdownStrategyListener;
use EonX\EasyBugsnag\ClientFactory;
use EonX\EasyBugsnag\Interfaces\ClientFactoryInterface;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $services->defaults()
        ->autoconfigure()
        ->autowire();

    // Client Factory + Client
    $services
        ->set(ClientFactoryInterface::class, ClientFactory::class)
        ->call('setConfigurators', [tagged_iterator(BridgeConstantsInterface::TAG_CLIENT_CONFIGURATOR)])
        ->call('setShutdownStrategy', [ref(ShutdownStrategyListener::class)]);

    $services
        ->set(Client::class)
        ->factory([ref(ClientFactoryInterface::class), 'create'])
        ->args(['%' . BridgeConstantsInterface::PARAM_API_KEY . '%']);

    // Shutdown Strategy
    $services
        ->set(ShutdownStrategyListener::class)
        ->tag('kernel.event_listener', ['event' => TerminateEvent::class])
        ->tag('kernel.event_listener', ['event' => ConsoleTerminateEvent::class]);
};
