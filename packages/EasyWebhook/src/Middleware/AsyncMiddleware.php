<?php

declare(strict_types=1);

namespace EonX\EasyWebhook\Middleware;

use EonX\EasyWebhook\Exceptions\WebhookIdRequiredForAsyncException;
use EonX\EasyWebhook\Interfaces\StackInterface;
use EonX\EasyWebhook\Interfaces\WebhookInterface;
use EonX\EasyWebhook\Interfaces\WebhookResultInterface;
use EonX\EasyWebhook\Interfaces\WebhookResultStoreInterface;
use EonX\EasyWebhook\WebhookResult;

final class AsyncMiddleware extends AbstractMiddleware
{
    /**
     * @var \EonX\EasyWebhook\Interfaces\AsyncDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var \EonX\EasyWebhook\Interfaces\WebhookResultStoreInterface
     */
    private $store;

    public function process(WebhookInterface $webhook, StackInterface $stack): WebhookResultInterface
    {
        if ($webhook->isSendNow()) {
            return $stack
                ->next()
                ->process($webhook, $stack);
        }

        $webhookResult = $this->store->store(new WebhookResult($webhook));

        if ($webhook->getId() === null) {
            throw new WebhookIdRequiredForAsyncException(\sprintf('
                WebhookResult must be persisted and have a unique identifier before being sent asynchronously.
                Please verify your %s implementation sets this identifier and is registered as a service
            ', WebhookResultStoreInterface::class));
        }

        return $this->dispatcher->dispatch($webhookResult);
    }
}
