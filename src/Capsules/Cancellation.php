<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Capsules;

use Countable;
use DateTimeImmutable;
use DOMDocument;
use PhpCfdi\XmlCancelacion\Definitions\DocumentType;
use PhpCfdi\XmlCancelacion\Internal\XmlHelperFunctions;

class Cancellation implements Countable, CapsuleInterface
{
    use XmlHelperFunctions;

    private const UUID_EXISTS = true;

    /** @var string */
    private $rfc;

    /** @var DateTimeImmutable */
    private $date;

    /** @var array<string, bool> This is a B-Tree array, values are stored in keys */
    private $uuids;

    /** @var DocumentType */
    private $documentType;

    /**
     * DTO for cancellation request, it supports CFDI and Retention
     *
     * @param string $rfc
     * @param string[] $uuids
     * @param DateTimeImmutable $date
     * @param DocumentType|null $type Uses CFDI if non provided
     */
    public function __construct(string $rfc, array $uuids, DateTimeImmutable $date, DocumentType $type = null)
    {
        $this->rfc = $rfc;
        $this->date = $date;
        $this->uuids = [];
        $this->documentType = $type ?? DocumentType::cfdi();
        foreach ($uuids as $uuid) {
            $this->uuids[strtoupper($uuid)] = self::UUID_EXISTS;
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

    public function documentType(): DocumentType
    {
        return $this->documentType;
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
        $document = (new BaseDocumentBuilder())->createBaseDocument('Cancelacion', $this->documentType->value());

        $cancelacion = $this->xmlDocumentElement($document);
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
