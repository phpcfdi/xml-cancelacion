<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Models;

use InvalidArgumentException;
use Stringable;

/**
 * Value object of a CFDI UUID
 */
final class Uuid implements Stringable
{
    private string $value;

    public function __construct(string $value)
    {
        if (! self::isValid($value)) {
            throw new InvalidArgumentException('Value is not a valid UUID');
        }
        $this->value = strtoupper($value);
    }

    public static function isValid(string $value): bool
    {
        return boolval(
            preg_match('/^[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}$/i', $value)
        );
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
