<?php

declare(strict_types=1);

namespace EonX\EasyWebhook\Tests;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use EonX\EasyWebhook\Interfaces\WebhookInterface;
use EonX\EasyWebhook\Webhook;

abstract class AbstractStoreTestCase extends AbstractTestCase
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $doctrineDbal;

    protected function createWebhookForSendAfter(
        ?\DateTimeInterface $sendAfter = null,
        ?string $status = null
    ): WebhookInterface {
        $webhook = Webhook::create('https://eonx.com', null, WebhookInterface::DEFAULT_METHOD);

        if ($sendAfter !== null) {
            $webhook->sendAfter($sendAfter);
        }

        if ($status !== null) {
            $webhook->status($status);
        }

        return $webhook;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function getDoctrineDbalConnection(): Connection
    {
        if ($this->doctrineDbal !== null) {
            return $this->doctrineDbal;
        }

        return $this->doctrineDbal = DriverManager::getConnection([
            'url' => 'sqlite:///:memory:',
        ]);
    }
}
