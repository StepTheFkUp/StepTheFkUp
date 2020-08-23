<?php

declare(strict_types=1);

namespace EonX\EasyWebhook\Interfaces;

interface WebhookInterface
{
    /**
     * @var string
     */
    public const DEFAULT_METHOD = 'POST';

    /**
     * @var string
     */
    public const STATUS_FAILED = 'failed';

    /**
     * @var string
     */
    public const STATUS_FAILED_PENDING_RETRY = 'failed_pending_retry';

    /**
     * @var string
     */
    public const STATUS_PENDING = 'pending';

    /**
     * @var string
     */
    public const STATUS_SUCCESS = 'success';

    /**
     * @param mixed[] $body
     */
    public function body(array $body): self;

    public function configured(?bool $configured = null): self;

    public function currentAttempt(int $currentAttempt): self;

    /**
     * @param mixed[] $extra
     */
    public function extra(array $extra): self;

    /**
     * @param mixed[] $data
     */
    public static function fromArray(array $data): self;

    /**
     * @return null|mixed[]
     */
    public function getBody(): ?array;

    public function getCurrentAttempt(): int;

    /**
     * @return null|mixed[]
     */
    public function getExtra(): ?array;

    /**
     * @return null|mixed[]
     */
    public function getHttpClientOptions(): ?array;

    public function getId(): ?string;

    public function getMaxAttempt(): int;

    public function getMethod(): ?string;

    public function getSecret(): ?string;

    public function getStatus(): string;

    public function getUrl(): ?string;

    /**
     * @param mixed[] $options
     */
    public function httpClientOptions(array $options): self;

    public function id(string $id): self;

    public function isConfigured(): bool;

    public function isSendNow(): bool;

    public function maxAttempt(int $maxAttempt): self;

    /**
     * @param mixed[] $extra
     */
    public function mergeExtra(array $extra): self;

    /**
     * @param mixed[] $options
     */
    public function mergeHttpClientOptions(array $options): self;

    public function method(string $method): self;

    public function secret(string $secret): self;

    public function sendNow(?bool $sendNow = null): self;

    public function status(string $status): self;

    /**
     * @return mixed[]
     */
    public function toArray(): array;

    public function url(string $url): self;
}
