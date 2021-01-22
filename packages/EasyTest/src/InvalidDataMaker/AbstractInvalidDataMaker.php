<?php

declare(strict_types=1);

namespace EonX\EasyTest\InvalidDataMaker;

use LogicException;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;

abstract class AbstractInvalidDataMaker
{
    /**
     * @var string
     */
    private const PLURAL_INDEX = '%count%';

    /**
     * @var string
     */
    protected $property;

    /**
     * @var string[]
     */
    private static $translations = [];

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface
     */
    private static $translator;

    /**
     * @var bool
     */
    private $asArrayElement = false;

    /**
     * @var bool
     */
    private $asString = false;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $propertyPath;

    /**
     * @var string
     */
    private $wrapWith;

    final public function __construct(string $property)
    {
        self::initTranslator();

        $this->property = $property;
    }

    final public static function addTranslations(string $translations): void
    {
        self::$translations[] = $translations;
    }

    final public static function make(string $property): self
    {
        return new static($property);
    }

    /**
     * @param mixed $value
     *
     * @return mixed[]
     */
    final protected function create($caseName, $value, ?string $message = null): array
    {
        if ($this->asString === true) {
            $value = (string)$value;
        }

        if ($this->asArrayElement === true) {
            $value = [$value];
        }

        $invalidData = [
            $this->property => $value,
        ];

        $data = [
            $caseName => [
                'data' => $invalidData,
                'message' => (string)($this->message ?? $message),
                'propertyPath' => $this->resolvePropertyPath($invalidData),
            ],
        ];

        if ($this->wrapWith !== null) {
            $data = $this->applyWrapWith($data);
        }

        return $data;
    }

    /**
     * @param mixed[]|null $params
     */
    final protected function translateMessage(string $messageKey, ?array $params = null, ?int $plural = null): string
    {
        $params[self::PLURAL_INDEX] = $plural;

        return self::$translator->trans($messageKey, $params);
    }

    private static function createTranslationLoader(string $extension): LoaderInterface
    {
        if (\in_array($extension, ['yaml', 'yml'], true)) {
            return new YamlFileLoader();
        }

        if ($extension === 'xlf') {
            return new XliffFileLoader();
        }

        throw new LogicException('For now allowed translations in formats [yaml, xlf]');
    }

    private static function initTranslator(): void
    {
        if (self::$translator !== null) {
            return;
        }

        $locale = 'en';
        $translator = new Translator($locale);

        foreach (self::$translations as $translation) {
            $extension = \strtolower(\pathinfo($translation, \PATHINFO_EXTENSION));
            $translator->addLoader($extension, self::createTranslationLoader($extension));
            $translator->addResource($extension, $translation, $locale);
        }

        self::$translator = $translator;
    }

    /**
     * @param mixed[] $data
     *
     * @return mixed[]
     */
    private function applyWrapWith(array $data): array
    {
        $caseName = \current(\array_keys($data));
        $caseData = $data[$caseName]['data'];

        /** @var string $newCaseName */
        $newCaseName = \str_replace($this->property, "{$this->wrapWith}.{$this->property}", $caseName);

        return [
            $newCaseName => [
                'data' => [
                    $this->wrapWith => $caseData,
                ],
                'message' => $data[$caseName]['message'],
                'propertyPath' => "{$this->wrapWith}.{$this->property}",
            ],
        ];
    }

    /**
     * @param mixed[] $invalidData
     *
     * @noinspection MultipleReturnStatementsInspection
     */
    private function resolvePropertyPath(array $invalidData): string
    {
        if ($this->propertyPath !== null) {
            return $this->propertyPath;
        }

        $propertyName = (string)\array_key_first($invalidData);

        if (\is_array($invalidData[$propertyName]) && \count($invalidData[$propertyName]) > 0) {
            // The case of stubs collection ('prop' => [ [], [], [], [] ])
            if (($invalidData[$propertyName][0] ?? null) === []) {
                return $propertyName;
            }

            $currentProperty = \current(\array_keys($invalidData[$propertyName]));

            if ($currentProperty === 0) {
                return $propertyName . '[0]';
            }

            return $propertyName . '.' . $this->resolvePropertyPath($invalidData[$propertyName]);
        }

        return $propertyName;
    }

    final public function asArrayElement(): self
    {
        $this->asArrayElement = true;

        return $this;
    }

    final public function asString(): self
    {
        $this->asString = true;

        return $this;
    }

    final public function message(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    final public function propertyPath(string $propertyPath): self
    {
        $this->propertyPath = $propertyPath;

        return $this;
    }

    final public function wrapWith(string $wrapWith): self
    {
        $this->wrapWith = $wrapWith;

        return $this;
    }
}
