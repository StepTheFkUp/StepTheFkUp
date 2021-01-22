<?php
declare(strict_types=1);

namespace EonX\EasyTest\InvalidDataMaker;

use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\CardScheme;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Currency;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\Luhn;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Timezone;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\Uuid;

class InvalidDataMaker extends AbstractInvalidDataMaker
{
    public function yieldArrayCollectionWithFewerItems(int $minElements): iterable
    {
        $value = new ArrayCollection(\array_fill(0, $minElements - 1, null));
        $message = $this->translateMessage(
            (new Count([
                'min' => $minElements,
            ]))->minMessage,
            [
                '{{ limit }}' => $minElements,
            ],
            $minElements
        );

        yield from $this->create("{$this->property} has too few elements in the collection is blank", $value, $message);
    }

    public function yieldArrayCollectionWithMoreItems(int $maxElements): iterable
    {
        $value = new ArrayCollection(\array_fill(0, $maxElements - 1, null));
        $message = $this->translateMessage(
            (new Count([
                'max' => $maxElements,
            ]))->maxMessage,
            [
                '{{ limit }}' => $maxElements,
            ],
            $maxElements
        );

        yield from $this->create("{$this->property} has too more elements in the collection", $value, $message);
    }

    public function yieldArrayWithFewerItems(int $minElements): iterable
    {
        $value = \array_fill(0, $minElements - 1, null);
        $message = $this->translateMessage(
            (new Count([
                'min' => $minElements,
            ]))->minMessage,
            [
                '{{ limit }}' => $minElements,
            ],
            $minElements
        );

        yield from $this->create("{$this->property} has too few elements in the array", $value, $message);
    }

    public function yieldArrayWithMoreItems(int $maxElements): iterable
    {
        $value = \array_fill(0, $maxElements + 1, null);
        $message = $this->translateMessage(
            (new Count([
                'max' => $maxElements,
            ]))->maxMessage,
            [
                '{{ limit }}' => $maxElements,
            ],
            $maxElements
        );

        yield from $this->create("{$this->property} has too many elements in the collection", $value, $message);
    }

    public function yieldBlankString(): iterable
    {
        $value = '';
        $message = $this->translateMessage((new NotBlank())->message);

        yield from $this->create("{$this->property} is blank", $value, $message);
    }

    public function yieldDateTimeLessThanOrEqualToNow(): iterable
    {
        $dateTime = Carbon::now();
        $message = $this->translateMessage(
            (new GreaterThan([
                'value' => 'now',
            ]))->message,
            [
                '{{ compared_value }}' => 'now',
            ]
        );

        $value = $dateTime->clone()->subSecond()->toAtomString();
        yield from $this->create("{$this->property} has less datetime", $value, $message);

        $value = $dateTime->toAtomString();
        yield from $this->create("{$this->property} has equal datetime", $value, $message);
    }

    public function yieldEmptyArrayCollection(int $minElements): iterable
    {
        $value = new ArrayCollection();
        $message = $this->translateMessage(
            (new Count([
                'min' => $minElements,
            ]))->minMessage,
            [
                '{{ limit }}' => $minElements,
            ],
            $minElements
        );

        yield from $this->create("{$this->property} has too few elements in the collection", $value, $message);
    }

    public function yieldIntegerGreaterThanGiven(int $lessThanOrEqualValue): iterable
    {
        $value = $lessThanOrEqualValue + 1;
        $message = $this->translateMessage(
            (new LessThanOrEqual([
                'value' => $value,
            ]))->message,
            [
                '{{ compared_value }}' => $lessThanOrEqualValue,
            ]
        );

        yield from $this->create("{$this->property} has greater value", $value, $message);
    }

    public function yieldIntegerGreaterThanOrEqualToGiven(int $lessThanValue): iterable
    {
        $value = $lessThanValue + 1;
        $message = $this->translateMessage(
            (new LessThan([
                'value' => $value,
            ]))->message,
            [
                '{{ compared_value }}' => $lessThanValue,
            ]
        );
        yield from $this->create("{$this->property} has greater value", $value, $message);

        $value = $lessThanValue;
        $message = $this->translateMessage(
            (new LessThan([
                'value' => $value,
            ]))->message,
            [
                '{{ compared_value }}' => $lessThanValue,
            ]
        );
        yield from $this->create("{$this->property} has equal value", $value, $message);
    }

    public function yieldInvalidChoice(): iterable
    {
        $value = 'invalid-choice';
        $message = $this->translateMessage((new Choice())->message);

        yield from $this->create("{$this->property} is not a valid choice", $value, $message);
    }

    public function yieldInvalidCreditCardNumber(): iterable
    {
        $value = '1111222233334444';
        $message = $this->translateMessage((new CardScheme([
            'schemes' => null,
        ]))->message);

        yield from $this->create("{$this->property} is not a valid credit card number", $value, $message);
    }

    public function yieldInvalidCurrencyCode(): iterable
    {
        $value = 'invalid-currency-code';
        $message = $this->translateMessage((new Currency())->message);

        yield from $this->create("{$this->property} is invalid currency", $value, $message);
    }

    public function yieldInvalidEmail(): iterable
    {
        $value = 'invalid-email';
        $message = $this->translateMessage((new Email())->message);

        yield from $this->create("{$this->property} is invalid email", $value, $message);
    }

    public function yieldInvalidExactLengthString(int $exactLength): iterable
    {
        $message = $this->translateMessage(
            (new Length([
                'max' => $exactLength,
                'min' => $exactLength,
            ]))->exactMessage,
            [
                '{{ limit }}' => $exactLength,
            ],
            $exactLength
        );

        $value = \str_pad('1', $exactLength + 1, '1');
        yield from $this->create("{$this->property} has length more than expected", $value, $message);

        $value = \str_pad('', $exactLength - 1, '1');
        yield from $this->create("{$this->property} has length less than expected", $value, $message);
    }

    public function yieldInvalidFloat(int $precision, ?int $integerPart = null): iterable
    {
        $value = ($integerPart ?? 0) + \round(1 / 3, $precision + 1);
        yield from $this->create("{$this->property} has invalid precision", $value);

        $value = 'abc';
        yield from $this->create("{$this->property} is a string", $value);

        $value = 10;
        yield from $this->create("{$this->property} is an integer", $value);
    }

    public function yieldInvalidTimezone(): iterable
    {
        $value = 'invalid-timezone';
        $message = $this->translateMessage((new Timezone())->message);

        yield from $this->create("{$this->property} is invalid timezone", $value, $message);
    }

    public function yieldInvalidUrl(): iterable
    {
        $value = 'invalid-url';
        $message = $this->translateMessage((new Url())->message);

        yield from $this->create("{$this->property} is invalid url", $value, $message);
    }

    public function yieldInvalidUuid(): iterable
    {
        $value = 'some-invalid-uuid';
        $message = $this->translateMessage((new Uuid())->message);

        yield from $this->create("{$this->property} is invalid uuid", $value, $message);
    }

    public function yieldNegativeNumber(): iterable
    {
        $value = -1;
        $message = $this->translateMessage((new PositiveOrZero())->message);

        yield from $this->create("{$this->property} has negative value", $value, $message);
    }

    public function yieldNegativeOrZeroNumber(): iterable
    {
        $message = $this->translateMessage((new Positive())->message);

        $value = -1;
        yield from $this->create("{$this->property} has negative value", $value, $message);

        $value = 0;
        yield from $this->create("{$this->property} has zero value", $value, $message);
    }

    public function yieldNonDigitSymbols(): iterable
    {
        $value = '111-aaa';
        $message = $this->translateMessage(
            (new Type([
                'type' => 'digit',
            ]))->message,
            [
                '{{ type }}' => 'digit',
            ]
        );

        yield from $this->create("{$this->property} has non-digit symbols", $value, $message);
    }

    public function yieldNonLuhnCreditCardNumber(): iterable
    {
        $value = '4388576018402626';
        $message = $this->translateMessage((new Luhn())->message);

        yield from $this->create("{$this->property} do not pass the Luhn algorithm", $value, $message);
    }

    public function yieldOutOfRangeNumber(int $min, int $max): iterable
    {
        $message = $this->translateMessage(
            (new Range(\compact('min', 'max')))->notInRangeMessage,
            [
                '{{ min }}' => $min,
                '{{ max }}' => $max,
            ]
        );

        $value = $max + 1;
        yield from $this->create("{$this->property} is out of range (above)", $value, $message);

        $value = $min - 1;
        yield from $this->create("{$this->property} is out of range (below)", $value, $message);
    }

    public function yieldTooLongString(int $maxLength): iterable
    {
        $value = \str_pad('g', $maxLength + 1, 'g');
        $message = $this->translateMessage(
            (new Length([
                'max' => $maxLength,
            ]))->maxMessage,
            [
                '{{ limit }}' => $maxLength,
            ],
            $maxLength
        );

        yield from $this->create("{$this->property} is too long", $value, $message);
    }

    public function yieldTooShortString(int $minLength): iterable
    {
        $value = $minLength > 1 ? \str_pad('g', $minLength - 1, 'g') : '';
        $message = $this->translateMessage(
            (new Length([
                'min' => $minLength,
            ]))->minMessage,
            [
                '{{ limit }}' => $minLength,
            ],
            $minLength
        );

        yield from $this->create("{$this->property} is too short", $value, $message);
    }
}
