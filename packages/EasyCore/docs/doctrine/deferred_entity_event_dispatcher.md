---eonx_docs---
title: Deferred Entity Event Dispatcher
weight: 3001
is_section: true
---eonx_docs---

### Setup

EntityManagerDecorator and EntityEventSubscriber:
```yaml
services:
    EonX\EasyCore\Doctrine\Dispatchers\DeferredEntityEventDispatcher:
        class: EonX\EasyCore\Doctrine\Dispatchers\DeferredEntityEventDispatcher

    EonX\EasyCore\Doctrine\ORM\Decorators\EntityManagerDecorator:
        arguments:
            $eventDispatcher: '@EonX\EasyCore\Doctrine\Dispatchers\DeferredEntityEventDispatcher'
            $wrapped: '@.inner'
        decorates: doctrine.orm.default_entity_manager

    EonX\EasyCore\Doctrine\Subscribers\EntityEventSubscriber:
        arguments:
            $eventDispatcher: '@EonX\EasyCore\Doctrine\Dispatchers\DeferredEntityEventDispatcher'
            $entities:
                - 'App\Entity\ApiResource\Order'
                - 'App\Entity\ApiResource\PaymentMethodInterface'
                - 'App\Entity\ApiResource\Transaction'
        tags:
            -
                name: doctrine.event_subscriber
                connection: default
```

Register the listener:
```yaml
services:
    App\Infrastructure\EasyWebhook\Listener\WebhookPaymentMethodCreatedListener:
        tags:
            -
                name: kernel.event_listener
                event: EonX\EasyCore\Doctrine\Events\EntityCreatedEvent

    App\Infrastructure\EasyWebhook\Listener\WebhookPaymentMethodUpdatedListener:
        tags:
            -
                name: kernel.event_listener
                event: EonX\EasyCore\Doctrine\Events\EntityUpdatedEvent
```

Listener:
```php
<?php
declare(strict_types=1);

namespace App\Infrastructure\EasyWebhook\Listener;

use App\Entity\ApiResource\PaymentMethodInterface;
use App\Infrastructure\Doctrine\Listener\AbstractEntityCreatedListener;

final class WebhookPaymentMethodCreatedListener extends AbstractEntityCreatedListener
{
    public function getEntityClass(): string
    {
        return PaymentMethodInterface::class;
    }

    public function handle(object $entity): void
    {
        /** @var \App\Entity\ApiResource\PaymentMethodInterface $paymentMethod */
        $paymentMethod = $entity;

        // do something
    }
}

```
