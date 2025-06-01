<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Models;

use LogicException;

/**
 * Document to cancel
 */
final class CancelDocument
{
    public function __construct(
        private readonly Uuid $uuid,
        private readonly CancelReason $reason,
        private readonly ?Uuid $substituteOf,
    ) {
    }

    public static function newWithErrorsRelated(string $uuid, string $substituteOf): self
    {
        return new self(new Uuid($uuid), CancelReason::withErrorsRelated(), new Uuid($substituteOf));
    }

    public static function newWithErrorsUnrelated(string $uuid): self
    {
        return new self(new Uuid($uuid), CancelReason::withErrorsUnrelated(), null);
    }

    public static function newNotExecuted(string $uuid): self
    {
        return new self(new Uuid($uuid), CancelReason::notExecuted(), null);
    }

    public static function newNormativeToGlobal(string $uuid): self
    {
        return new self(new Uuid($uuid), CancelReason::normativeToGlobal(), null);
    }

    public function uuid(): Uuid
    {
        return $this->uuid;
    }

    public function reason(): CancelReason
    {
        return $this->reason;
    }

    public function hasSubstituteOf(): bool
    {
        return null !== $this->substituteOf;
    }

    public function substituteOf(): Uuid
    {
        if (null === $this->substituteOf) {
            throw new LogicException('The property substituteOf is not defined');
        }

        return $this->substituteOf;
    }
}
