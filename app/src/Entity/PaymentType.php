<?php

declare(strict_types=1);

namespace App\Entity;

use Webmozart\Assert\Assert;

class PaymentType
{
    public const PAYPAL = 'paypal';
    public const STRIPE = 'stripe';

    private string $value;

    public function __construct(string $value)
    {
        Assert::oneOf($value, [
            self::PAYPAL,
            self::STRIPE,
        ]);

        $this->value = $value;
    }

    public static function stripe(): self
    {
        return new self(self::STRIPE);
    }

    public static function paypal(): self
    {
        return new self(self::PAYPAL);
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
