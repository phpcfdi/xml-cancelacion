<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Capsules;

use Countable;
use DateTimeImmutable;
use DOMDocument;
use PhpCfdi\XmlCancelacion\Models\DocumentType;
use PhpCfdi\XmlCancelacion\Internal\XmlHelperFunctions;
use PhpCfdi\XmlCancelacion\Models\CancelDocuments;

class Cancellation implements Countable, CapsuleInterface
{
    use XmlHelperFunctions;

    /** @var string */
    private $rfc;

    /** @var DateTimeImmutable */
    private $date;

    /** @var CancelDocuments */
    private $documents;

    /** @var DocumentType */
    private $documentType;

    /**
     * DTO for cancellation request, it supports CFDI and Retention
     *
     * @param string $rfc
     * @param CancelDocuments $documents
     * @param DateTimeImmutable $date
     * @param DocumentType|null $type Uses CFDI if non provided
     */
    public function __construct(
        string $rfc,
        CancelDocuments $documents,
        DateTimeImmutable $date,
        DocumentType $type = null
    ) {
        $this->rfc = $rfc;
        $this->date = $date;
        $this->documents = $documents;
        $this->documentType = $type ?? DocumentType::cfdi();
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

    public function documents(): CancelDocuments
    {
        return $this->documents;
    }

    public function count(): int
    {
        return $this->documents->count();
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function exportToDocument(): DOMDocument
    {
        $document = (new BaseDocumentBuilder())->createBaseDocument('Cancelacion', $this->documentType->value());

        $cancelacion = $this->xmlDocumentElement($document);
        $cancelacion->setAttribute('RfcEmisor', $this->rfc()); // en el anexo 20 es opcional!
        $cancelacion->setAttribute('Fecha', $this->date()->format('Y-m-d\TH:i:s'));

        $folios = $document->createElement('Folios');
        $cancelacion->appendChild($folios);
        foreach ($this->documents as $cancelDocument) {
            $folio = $document->createElement('Folio');
            $folios->appendChild($folio);
            $subsituteOf = $cancelDocument->hasSubstituteOf() ? (string) $cancelDocument->substituteOf() : '';
            $folio->setAttribute('UUID', (string) $cancelDocument->uuid());
            $folio->setAttribute('Motivo', (string) $cancelDocument->reason());
            $folio->setAttribute('FolioSustitucion', $subsituteOf);
        }

        return $document;
    }

    public function belongsToRfc(string $rfc): bool
    {
        return ($rfc === $this->rfc());
    }
}
