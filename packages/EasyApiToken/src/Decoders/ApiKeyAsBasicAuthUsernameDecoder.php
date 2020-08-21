<?php

declare(strict_types=1);

namespace EonX\EasyApiToken\Decoders;

use EonX\EasyApiToken\Interfaces\ApiTokenInterface;
use EonX\EasyApiToken\Tokens\ApiKey;
use Symfony\Component\HttpFoundation\Request;

/**
 * @deprecated since 2.4. Will be removed in 3.0. Use EonX\EasyApiToken\Decoders\ApiKeyDecoder instead.
 */
final class ApiKeyAsBasicAuthUsernameDecoder extends AbstractApiTokenDecoder
{
    public function __construct(?string $name = null)
    {
        @\trigger_error(
            \sprintf(
                'Using %s is deprecated since 2.4 and will be removed in 3.0. Use %s instead',
                ApiKeyAsBasicAuthUsernameDecoder::class,
                ApiKeyDecoder::class
            ),
            \E_USER_DEPRECATED
        );

        parent::__construct($name ?? self::NAME_USER_APIKEY);
    }

    public function decode(Request $request): ?ApiTokenInterface
    {
        $authorization = $this->getHeaderWithoutPrefix('Authorization', 'Basic', $request);

        if ($authorization === null) {
            return null; // If Authorization doesn't start with Basic, return null
        }

        $authorization = \explode(':', (string)\base64_decode($authorization, true));

        if (empty(\trim($authorization[0] ?? '')) === true || empty(\trim($authorization[1] ?? '')) === false) {
            return null; // If Authorization doesn't contain ONLY a username, return null
        }

        return new ApiKey(\trim($authorization[0]));
    }
}
