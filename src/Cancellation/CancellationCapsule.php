<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Cancellation;

use Countable;
use DateTimeImmutable;
use DOMDocument;
use PhpCfdi\XmlCancelacion\Contracts\CapsuleInterface;

class CancellationCapsule implements Countable, CapsuleInterface
{
    /** @var string */
    private $rfc;

    /** @var DateTimeImmutable */
    private $date;

    /** @var array This is a B-Tree array, values are stored in keys */
    private $uuids;

    public function __construct(string $rfc, array $uuids, DateTimeImmutable $date)
    {
        $this->rfc = $rfc;
        $this->date = $date;
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

    public function exportToDocument(): DOMDocument
    {
        return (new CancellationDocumentBuilder())->makeDocument($this);
    }

    public function belongsToRfc(string $rfc): bool
    {
        return ($rfc === $this->rfc());
    }
}
