<?php

declare(strict_types=1);

namespace EonX\EasyWebhook;

use EonX\EasyWebhook\Interfaces\WebhookInterface;

abstract class AbstractWebhook implements WebhookInterface
{
    /**
     * @var string[]
     */
    protected static $integers = [self::OPTION_CURRENT_ATTEMPT, self::OPTION_MAX_ATTEMPT];

    /**
     * @var string[]
     */
    protected static $setters = [
        self::OPTION_BODY => 'body',
        self::OPTION_BODY_AS_STRING => 'bodyAsString',
        self::OPTION_CURRENT_ATTEMPT => 'currentAttempt',
        self::OPTION_EVENT => 'event',
        self::OPTION_ID => 'id',
        self::OPTION_HTTP_OPTIONS => 'httpClientOptions',
        self::OPTION_MAX_ATTEMPT => 'maxAttempt',
        self::OPTION_METHOD => 'method',
        self::OPTION_SECRET => 'secret',
        self::OPTION_SEND_AFTER => 'sendAfter',
        self::OPTION_STATUS => 'status',
        self::OPTION_URL => 'url',
    ];

    /**
     * @var null|bool
     */
    private $allowRerun;

    /**
     * @var null|mixed[]
     */
    private $body;

    /**
     * @var null|string
     */
    private $bodyAsString;

    /**
     * @var null|bool
     */
    private $bypassSendAfter;

    /**
     * @var null|bool
     */
    private $configured;

    /**
     * @var null|int
     */
    private $currentAttempt;

    /**
     * @var null|string
     */
    private $event;

    /**
     * @var null|mixed[]
     */
    private $extra;

    /**
     * @var mixed[]
     */
    private $headers;

    /**
     * @var null|mixed[]
     */
    private $httpClientOptions;

    /**
     * @var null|string
     */
    private $id;

    /**
     * @var null|int
     */
    private $maxAttempt;

    /**
     * @var null|string
     */
    private $method;

    /**
     * @var mixed[]
     */
    private $queries;

    /**
     * @var null|string
     */
    private $secret;

    /**
     * @var null|\DateTimeInterface
     */
    private $sendAfter;

    /**
     * @var null|bool
     */
    private $sendNow;

    /**
     * @var null|string
     */
    private $status = self::STATUS_PENDING;

    /**
     * @var null|string
     */
    private $url;

    /**
     * @param null|mixed[] $body
     */
    public static function create(string $url, ?array $body = null, ?string $method = null): WebhookInterface
    {
        $webhook = (new static())->url($url)
            ->method($method ?? self::DEFAULT_METHOD);

        if ($body !== null) {
            $webhook->body($body);
        }

        return $webhook;
    }

    /**
     * @param mixed[] $data
     */
    public static function fromArray(array $data): WebhookInterface
    {
        $webhook = new static();

        foreach (static::$setters as $name => $setter) {
            if (($data[$name] ?? null) !== null) {
                $value = $data[$name];

                if (\in_array($name, static::$integers, true)) {
                    $value = (int)$value;
                }

                $webhook->{$setter}($value);
            }
        }

        return $webhook;
    }

    public function allowRerun(?bool $allowRerun = null): WebhookInterface
    {
        $this->allowRerun = $allowRerun ?? true;

        return $this;
    }

    /**
     * @param mixed[] $body
     */
    public function body(array $body): WebhookInterface
    {
        $this->body = $body;

        return $this;
    }

    public function bodyAsString(string $body): WebhookInterface
    {
        $this->bodyAsString = $body;

        return $this;
    }

    public function bypassSendAfter(?bool $bypassSendAfter = null): WebhookInterface
    {
        $this->bypassSendAfter = $bypassSendAfter;

        return $this;
    }

    public function configured(?bool $configured = null): WebhookInterface
    {
        $this->configured = $configured ?? true;

        return $this;
    }

    public function currentAttempt(int $currentAttempt): WebhookInterface
    {
        $this->currentAttempt = $currentAttempt;

        return $this;
    }

    public function event(string $event): WebhookInterface
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @param mixed[] $extra
     */
    public function extra(array $extra): WebhookInterface
    {
        $this->extra = $extra;

        return $this;
    }

    /**
     * @return null|mixed[]
     */
    public function getBody(): ?array
    {
        return $this->body;
    }

    public function getBodyAsString(): ?string
    {
        return $this->bodyAsString;
    }

    public function getCurrentAttempt(): int
    {
        return $this->currentAttempt ?? self::DEFAULT_CURRENT_ATTEMPT;
    }

    public function getEvent(): ?string
    {
        return $this->event;
    }

    /**
     * @return null|mixed[]
     */
    public function getExtra(): ?array
    {
        return $this->extra;
    }

    /**
     * @return null|mixed[]
     */
    public function getHttpClientOptions(): ?array
    {
        if ($this->headers === null && $this->queries === null && $this->httpClientOptions === null) {
            return null;
        }

        $return = $this->httpClientOptions ?? [];

        if ($this->headers !== null) {
            $return['headers'] = \array_merge($return['headers'] ?? [], $this->headers);
        }

        if ($this->queries !== null) {
            $return['query'] = \array_merge($return['query'] ?? [], $this->queries);
        }

        return $return;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getMaxAttempt(): int
    {
        return $this->maxAttempt ?? 1;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function getSecret(): ?string
    {
        return $this->secret;
    }

    public function getSendAfter(): ?\DateTimeInterface
    {
        return $this->sendAfter;
    }

    public function getStatus(): string
    {
        return $this->status ?? WebhookInterface::STATUS_PENDING;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param mixed $value
     */
    public function header(string $name, $value): WebhookInterface
    {
        if ($this->headers === null) {
            $this->headers = [];
        }

        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * @param mixed[] $headers
     */
    public function headers(array $headers): WebhookInterface
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @param mixed[] $options
     */
    public function httpClientOptions(array $options): WebhookInterface
    {
        $this->httpClientOptions = $options;

        return $this;
    }

    public function id(string $id): WebhookInterface
    {
        $this->id = $id;

        return $this;
    }

    public function isConfigured(): bool
    {
        return $this->configured ?? false;
    }

    public function isRerunAllowed(): bool
    {
        return $this->allowRerun ?? false;
    }

    public function isSendAfterBypassed(): bool
    {
        return $this->bypassSendAfter ?? false;
    }

    public function isSendNow(): bool
    {
        return $this->sendNow ?? false;
    }

    public function maxAttempt(int $maxAttempt): WebhookInterface
    {
        $this->maxAttempt = $maxAttempt;

        return $this;
    }

    /**
     * @param mixed[] $extra
     */
    public function mergeExtra(array $extra): WebhookInterface
    {
        $this->extra = \array_merge_recursive($this->extra ?? [], $extra);

        return $this;
    }

    /**
     * @param mixed[] $options
     */
    public function mergeHttpClientOptions(array $options): WebhookInterface
    {
        $this->httpClientOptions = \array_merge_recursive($this->httpClientOptions ?? [], $options);

        return $this;
    }

    public function method(string $method): WebhookInterface
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @param mixed[] $queries
     */
    public function queries(array $queries): WebhookInterface
    {
        $this->queries = $queries;

        return $this;
    }

    /**
     * @param mixed $value
     */
    public function query(string $name, $value): WebhookInterface
    {
        if ($this->queries === null) {
            $this->queries = [];
        }

        $this->queries[$name] = $value;

        return $this;
    }

    public function secret(string $secret): WebhookInterface
    {
        $this->secret = $secret;

        return $this;
    }

    public function sendAfter(\DateTimeInterface $after): WebhookInterface
    {
        $this->sendAfter = $after;

        return $this;
    }

    public function sendNow(?bool $sendNow = null): WebhookInterface
    {
        $this->sendNow = $sendNow ?? true;

        return $this;
    }

    public function status(string $status): WebhookInterface
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [
            'current_attempt' => $this->getCurrentAttempt(),
            'event' => $this->getEvent(),
            'http_options' => $this->getHttpClientOptions(),
            'max_attempt' => $this->getMaxAttempt(),
            'method' => $this->getMethod(),
            'send_after' => $this->getSendAfter(),
            'status' => $this->getStatus(),
            'url' => $this->getUrl(),
        ];
    }

    public function url(string $url): WebhookInterface
    {
        $this->url = $url;

        return $this;
    }
}
