<?php

declare(strict_types=1);

namespace EonX\EasyErrorHandler\Bridge\Symfony\Builders;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use EonX\EasyErrorHandler\Interfaces\ErrorResponseBuilderProviderInterface;

final class ApiPlatformBuilderProvider implements ErrorResponseBuilderProviderInterface
{
    /**
     * @var mixed[]
     */
    private $keys;

    /**
     * @param null|mixed[] $keys
     */
    public function __construct(?array $keys = null)
    {
        $this->keys = $keys ?? [];
    }

    /**
     * @return iterable<\EonX\EasyErrorHandler\Interfaces\ErrorResponseBuilderInterface>
     */
    public function getBuilders(): iterable
    {
        if (\class_exists(ValidationException::class)) {
            yield new ApiPlatformValidationExceptionBuilder($this->keys);
        }
    }
}
