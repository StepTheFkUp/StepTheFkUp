<?php
declare(strict_types=1);

namespace EonX\EasySecurity\Bridge\Symfony\Factories;

use EonX\EasySecurity\Bridge\Symfony\Interfaces\AuthenticationFailureResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

final class AuthenticationFailureResponseFactory implements AuthenticationFailureResponseFactoryInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * AuthenticationFailureResponseFactory constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Create authentication failure response.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param null|\Symfony\Component\Security\Core\Exception\AuthenticationException $exception
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request, ?AuthenticationException $exception = null): Response
    {
        if ($exception !== null) {
            $this->logger->info('Authentication exception', [
                'message' => $exception->getMessageKey(),
                'data' => $exception->getMessageData()
            ]);
        }

        $data = [
            'error' => 'Unauthorized',
            'code' => JsonResponse::HTTP_UNAUTHORIZED,
            'sub_code' => 0
        ];

        return new JsonResponse($data, JsonResponse::HTTP_UNAUTHORIZED);
    }
}
