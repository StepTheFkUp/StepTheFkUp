<?php
declare(strict_types=1);

namespace LoyaltyCorp\EasyApiToken\Decoders;

use Psr\Http\Message\ServerRequestInterface;
use LoyaltyCorp\EasyApiToken\Interfaces\EasyApiTokenDecoderInterface;
use LoyaltyCorp\EasyApiToken\Interfaces\EasyApiTokenInterface;
use LoyaltyCorp\EasyApiToken\Interfaces\Tokens\Factories\JwtEasyApiTokenFactoryInterface;
use LoyaltyCorp\EasyApiToken\Traits\EasyApiTokenDecoderTrait;

final class JwtTokenDecoder implements EasyApiTokenDecoderInterface
{
    use EasyApiTokenDecoderTrait;

    /**
     * @var \LoyaltyCorp\EasyApiToken\Interfaces\Tokens\Factories\JwtEasyApiTokenFactoryInterface
     */
    private $jwtEasyApiTokenFactory;

    /**
     * JwtTokenDecoder constructor.
     *
     * @param \LoyaltyCorp\EasyApiToken\Interfaces\Tokens\Factories\JwtEasyApiTokenFactoryInterface $jwtEasyApiTokenFactory
     */
    public function __construct(JwtEasyApiTokenFactoryInterface $jwtEasyApiTokenFactory)
    {
        $this->jwtEasyApiTokenFactory = $jwtEasyApiTokenFactory;
    }

    /**
     * Decode API token for given request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return null|\LoyaltyCorp\EasyApiToken\Interfaces\EasyApiTokenInterface
     *
     * @throws \LoyaltyCorp\EasyApiToken\Exceptions\InvalidEasyApiTokenFromRequestException
     */
    public function decode(ServerRequestInterface $request): ?EasyApiTokenInterface
    {
        $authorization = $this->getHeaderWithoutPrefix('Authorization', 'Bearer', $request);

        if ($authorization === null) {
            return null; // If Authorization doesn't start with Basic, return null
        }

        return $this->jwtEasyApiTokenFactory->createFromString($authorization);
    }
}

\class_alias(
    JwtTokenDecoder::class,
    'StepTheFkUp\EasyApiToken\Decoders\JwtTokenDecoder',
    false
);
