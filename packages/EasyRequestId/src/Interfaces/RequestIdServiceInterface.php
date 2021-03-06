<?php

declare(strict_types=1);

namespace EonX\EasyRequestId\Interfaces;

interface RequestIdServiceInterface
{
    /**
     * @var string
     */
    public const DEFAULT_HTTP_HEADER_CORRELATION_ID = 'X-EONX-CORRELATION-ID';

    /**
     * @var string
     */
    public const DEFAULT_HTTP_HEADER_REQUEST_ID = 'X-EONX-REQUEST-ID';

    /**
     * @var string
     */
    public const KEY_RESOLVED_CORRELATION_ID = 'resolved_correlation_id';

    /**
     * @var string
     */
    public const KEY_RESOLVED_REQUEST_ID = 'resolved_request_id';

    public function getCorrelationId(): string;

    public function getCorrelationIdHeaderName(): string;

    public function getRequestId(): string;

    public function getRequestIdHeaderName(): string;

    public function setResolver(callable $resolver): self;
}
