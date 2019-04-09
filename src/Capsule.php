<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion;

use DateTimeImmutable;

class Capsule
{
    /** @var string */
    private $rfc;

    /** @var DateTimeImmutable */
    private $date;

    /** @var array */
    private $uuids;

    public function __construct(string $rfc, array $uuids = [], DateTimeImmutable $date = null)
    {
        $this->rfc = $rfc;
        $this->date = $date ?? new DateTimeImmutable('now');
        foreach ($uuids as $uuid) {
            $this->append($uuid);
        }
    }

    public function rfc(): string
    {
        return $this->rfc;
    }

    public function date(): DateTimeImmutable
    {
        return $this->date;
    }

    public function uuids(): array
    {
        return array_keys($this->uuids);
    }

    public function append(string $uuid): void
    {
        $this->uuids[strtoupper($uuid)] = true;
    }

    public function count(): int
    {
        return count($this->uuids);
    }
}
