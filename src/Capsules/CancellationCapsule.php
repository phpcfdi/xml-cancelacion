<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Capsules;

use Countable;
use DateTimeImmutable;
use DOMDocument;
use DOMElement;

class CancellationCapsule implements Countable, CapsuleInterface
{
    /** @var string */
    private $rfc;

    /** @var DateTimeImmutable */
    private $date;

    /** @var array<string, bool> This is a B-Tree array, values are stored in keys */
    private $uuids;

    public function __construct(string $rfc, array $uuids, DateTimeImmutable $date)
    {
        $this->rfc = $rfc;
        $this->date = $date;
        $this->uuids = [];
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

    /**
     * The list of UUIDS
     * @return string[]
     */
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
        $document = (new BaseDocumentBuilder())->createBaseDocument('Cancelacion', 'http://cancelacfd.sat.gob.mx');

        /** @var DOMElement $cancelacion */
        $cancelacion = $document->documentElement;
        $cancelacion->setAttribute('RfcEmisor', $this->rfc()); // en el anexo 20 es opcional!
        $cancelacion->setAttribute('Fecha', $this->date()->format('Y-m-d\TH:i:s'));
        $folios = $cancelacion->appendChild($document->createElement('Folios'));
        foreach ($this->uuids() as $uuid) {
            $folios->appendChild($document->createElement('UUID', htmlspecialchars($uuid, ENT_XML1)));
        }

        return $document;
    }

    public function belongsToRfc(string $rfc): bool
    {
        return ($rfc === $this->rfc());
    }
}
