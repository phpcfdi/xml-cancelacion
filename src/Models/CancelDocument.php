<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Models;

/**
 * Document to cancel
 */
final class CancelDocument
{
    /** @var Uuid */
    private $uuid;

    /** @var CancelReason */
    private $reason;

    /** @var Uuid|null */
    private $substituteOf;

    public function __construct(Uuid $uuid, CancelReason $reason, ?Uuid $substituteOf = null)
    {
        $this->uuid = $uuid;
        $this->reason = $reason;
        $this->substituteOf = $substituteOf;
    }

    public static function newWithErrorsRelated(string $uuid, string $substituteOf): self
    {
        return new self(new Uuid($uuid), CancelReason::withErrorsRelated(), new Uuid($substituteOf));
    }

    public static function newWithErrorsUnrelated(string $uuid): self
    {
        return new self(new Uuid($uuid), CancelReason::withErrorsUnrelated());
    }

    public static function newNotExecuted(string $uuid): self
    {
        return new self(new Uuid($uuid), CancelReason::notExecuted());
    }

    public static function newNormativeToGlobal(string $uuid): self
    {
        return new self(new Uuid($uuid), CancelReason::normativeToGlobal());
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
            throw new \LogicException('The property substituteOf is not defined');
        }

        return $this->substituteOf;
    }
}
