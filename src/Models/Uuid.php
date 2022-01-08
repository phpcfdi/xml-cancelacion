<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Models;

final class Uuid
{
    /** @var string */
    private $value;

    public function __construct(string $value)
    {
        if (! self::isValid($value)) {
            throw new \InvalidArgumentException('Value is not a valid UUID');
        }
        $this->value = strtoupper($value);
    }

    public static function isValid(string $value): bool
    {
        return boolval(
            preg_match('/^[a-f0-9A-F]{8}-[a-f0-9A-F]{4}-[a-f0-9A-F]{4}-[a-f0-9A-F]{4}-[a-f0-9A-F]{12}$/', $value)
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
