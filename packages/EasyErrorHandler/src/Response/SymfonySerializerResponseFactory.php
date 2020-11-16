<?php

declare(strict_types=1);

namespace EonX\EasyErrorHandler\Response;

use EonX\EasyErrorHandler\Interfaces\ErrorResponseDataInterface;
use EonX\EasyErrorHandler\Interfaces\ErrorResponseFactoryInterface;
use EonX\EasyErrorHandler\Response\Data\ErrorResponseFormat;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

final class SymfonySerializerResponseFactory implements ErrorResponseFactoryInterface
{
    /**
     * @var mixed[]
     */
    private $errorFormats;

    /**
     * @var \Symfony\Component\Serializer\SerializerInterface
     */
    private $serializer;

    /**
     * @param null|mixed[] $errorFormats
     */
    public function __construct(SerializerInterface $serializer, ?array $errorFormats = null)
    {
        $this->serializer = $serializer;
        $this->errorFormats = $errorFormats ?? ['json' => ['application/json']];
    }

    public function create(Request $request, ErrorResponseDataInterface $data): Response
    {
        $format = $this->getFormat($request);

        $headers = $data->getHeaders();
        $headers['Content-Type'] = \sprintf('%s; charset=utf-8', $format->getValue());
        $headers['X-Content-Type-Options'] = 'nosniff';
        $headers['X-Frame-Options'] = 'deny';

        $content = $this->serializer->serialize(
            $data->getRawData(),
            $format->getKey(),
            ['statusCode' => $data->getStatusCode()]
        );

        return new Response($content, $data->getStatusCode(), $headers);
    }

    private function getFormat(Request $request): ErrorResponseFormat
    {
        $requestFormat = $request->getRequestFormat('');

        if ('' !== $requestFormat && isset($this->errorFormats[$requestFormat])) {
            return ErrorResponseFormat::create($requestFormat, $this->errorFormats[$requestFormat][0]);
        }

        $requestMimeTypes = Request::getMimeTypes($request->getRequestFormat());
        $errorFormat = null;

        foreach ($this->errorFormats as $format => $errorMimeTypes) {
            if (\array_intersect($requestMimeTypes, $errorMimeTypes) || $errorFormat === null) {
                $errorFormat = ErrorResponseFormat::create($format, $errorMimeTypes[0]);
            }
        }

        return $errorFormat;
    }
}
