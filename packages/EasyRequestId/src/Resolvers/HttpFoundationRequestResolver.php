<?php

declare(strict_types=1);

namespace EonX\EasyRequestId\Resolvers;

use EonX\EasyRequestId\Interfaces\RequestIdServiceInterface;
use Symfony\Component\HttpFoundation\Request;

final class HttpFoundationRequestResolver
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * @var \EonX\EasyRequestId\Interfaces\RequestIdServiceInterface
     */
    private $requestIdService;

    public function __construct(Request $request, RequestIdServiceInterface $requestIdService)
    {
        $this->request = $request;
        $this->requestIdService = $requestIdService;
    }

    /**
     * @return null[]|string[]
     */
    public function __invoke(): array
    {
        $correlationIdHeader = $this->getHeader($this->requestIdService->getCorrelationIdHeaderName());
        $requestIdHeader = $this->getHeader($this->requestIdService->getRequestIdHeaderName());

        return [
            RequestIdServiceInterface::KEY_RESOLVED_CORRELATION_ID => $correlationIdHeader,
            RequestIdServiceInterface::KEY_RESOLVED_REQUEST_ID => $requestIdHeader,
        ];
    }

    private function getHeader(string $header): ?string
    {
        $value = $this->request->headers->get($header);

        return \is_string($value) && $value !== '' ? $value : null;
    }
}
