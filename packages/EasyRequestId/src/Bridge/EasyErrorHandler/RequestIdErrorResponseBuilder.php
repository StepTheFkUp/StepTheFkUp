<?php

declare(strict_types=1);

namespace EonX\EasyRequestId\Bridge\EasyErrorHandler;

use EonX\EasyErrorHandler\Builders\AbstractErrorResponseBuilder;
use EonX\EasyErrorHandler\Interfaces\ErrorResponseBuilderProviderInterface;
use EonX\EasyRequestId\Interfaces\RequestIdKeysAwareInterface;
use EonX\EasyRequestId\Interfaces\RequestIdServiceInterface;
use EonX\EasyRequestId\Traits\RequestIdKeysAwareTrait;
use Throwable;

final class RequestIdErrorResponseBuilder extends AbstractErrorResponseBuilder implements RequestIdKeysAwareInterface, ErrorResponseBuilderProviderInterface
{
    use RequestIdKeysAwareTrait;

    /**
     * @var \EonX\EasyRequestId\Interfaces\RequestIdServiceInterface
     */
    private $requestIdService;

    public function __construct(RequestIdServiceInterface $requestIdService, ?int $priority = null)
    {
        $this->requestIdService = $requestIdService;

        parent::__construct($priority);
    }

    public function buildHeaders(Throwable $throwable, ?array $headers = null): ?array
    {
        $headers = $headers ?? [];
        $headers[$this->getCorrelationIdKey()] = $this->requestIdService->getCorrelationId();
        $headers[$this->getRequestIdKey()] = $this->requestIdService->getRequestId();

        return parent::buildHeaders($throwable, $headers);
    }

    /**
     * @return iterable<\EonX\EasyErrorHandler\Interfaces\ErrorResponseBuilderInterface>
     */
    public function getBuilders(): iterable
    {
        yield $this;
    }
}
