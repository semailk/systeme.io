<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class CodeType extends StringType
{
    public const NAME = 'currency_code';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof Code ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        /** @var string $value */
        return !empty($value) ? new Code($value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
