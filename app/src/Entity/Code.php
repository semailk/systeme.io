<?php

declare(strict_types=1);

namespace App\Entity;

use InvalidArgumentException;

class Code
{
    private string $value;

    public function __construct(string $value)
    {
        if (\strlen($value) > 3) {
            throw new InvalidArgumentException('CODE: Максимальное количество символов 3.');
        }

        if (preg_match('/^[a-zA-Z]+$/', $value) !== 1) {
            throw new InvalidArgumentException('Code может состоять только из латинский символов.');
        }

        $this->value = strtoupper($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
