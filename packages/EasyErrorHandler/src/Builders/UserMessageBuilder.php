<?php

declare(strict_types=1);

namespace EonX\EasyErrorHandler\Builders;

use EonX\EasyErrorHandler\Bridge\BridgeConstantsInterface;
use EonX\EasyErrorHandler\Interfaces\Exceptions\TranslatableExceptionInterface;
use EonX\EasyErrorHandler\Interfaces\TranslatorInterface;
use Throwable;

final class UserMessageBuilder extends AbstractSingleKeyErrorResponseBuilder
{
    /**
     * @var string
     */
    protected $runtimeName;

    /**
     * @var \EonX\EasyErrorHandler\Interfaces\TranslatorInterface
     */
    private $translator;

    public function __construct(
        TranslatorInterface $translator,
        string $runtimeName,
        ?string $key = null,
        ?int $priority = null
    ) {
        $this->translator = $translator;
        $this->runtimeName = $runtimeName;

        parent::__construct($key, $priority);
    }

    /**
     * @param mixed[] $data
     *
     * @return string
     */
    protected function doBuildValue(Throwable $throwable, array $data)
    {
        $message = null;
        $parameters = [];

        if ($throwable instanceof TranslatableExceptionInterface) {
            $message = $this->processUserMessage($throwable->getUserMessage());
            $parameters = $throwable->getUserMessageParams();
        }

        return $this->translator->trans(
            $message ?? TranslatableExceptionInterface::DEFAULT_USER_MESSAGE,
            $parameters
        );
    }

    protected function getDefaultKey(): string
    {
        return 'message';
    }

    private function processUserMessage(string $userMessage): string
    {
        if (\in_array($this->runtimeName, ['lumen', 'laravel'])) {
            return \implode([BridgeConstantsInterface::TRANSLATION_NAMESPACE, '::', $userMessage]);
        }

        return $userMessage;
    }
}
