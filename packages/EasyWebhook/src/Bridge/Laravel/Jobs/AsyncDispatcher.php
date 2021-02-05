<?php

declare(strict_types=1);

namespace EonX\EasyWebhook\Bridge\Laravel\Jobs;

use EonX\EasyWebhook\Interfaces\AsyncDispatcherInterface;
use EonX\EasyWebhook\Interfaces\WebhookResultInterface;
use Illuminate\Contracts\Bus\Dispatcher;

final class AsyncDispatcher implements AsyncDispatcherInterface
{
    /**
     * @var \Illuminate\Contracts\Bus\Dispatcher
     */
    private $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function dispatch(WebhookResultInterface $webhookResult): WebhookResultInterface
    {
        $webhook = $webhookResult->getWebhook();

        if ($webhook->getId() !== null) {
            $this->dispatcher->dispatch(new SendWebhookJob($webhook->getId(), $webhook->getMaxAttempt()));
        }

        return $webhookResult;
    }
}
