<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion;

use Countable;
use DateTimeImmutable;

class Capsule implements Countable
{
    /** @var string */
    private $rfc;

    /** @var DateTimeImmutable */
    private $date;

    /** @var array This is a B-Tree array, values are stored in keys */
    private $uuids;

    public function __construct(string $rfc, array $uuids = [], DateTimeImmutable $date = null)
    {
        $this->rfc = $rfc;
        $this->date = $date ?? new DateTimeImmutable('now');
        foreach ($uuids as $uuid) {
            $this->uuids[strtoupper($uuid)] = true;
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

    public function count(): int
    {
        return count($this->uuids);
    }
}
