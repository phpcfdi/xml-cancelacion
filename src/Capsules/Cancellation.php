<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Capsules;

use Countable;
use DateTimeImmutable;
use DOMDocument;
use PhpCfdi\XmlCancelacion\Internal\XmlHelperFunctions;
use PhpCfdi\XmlCancelacion\Models\CancelDocuments;
use PhpCfdi\XmlCancelacion\Models\DocumentType;

class Cancellation implements Countable, CapsuleInterface
{
    use XmlHelperFunctions;

    private DocumentType $documentType;

    /**
     * DTO for cancellation request, it supports CFDI and Retention
     *
     * @param DocumentType|null $type Uses CFDI if non provided
     */
    public function __construct(
        private readonly string $rfc,
        private readonly CancelDocuments $documents,
        private readonly DateTimeImmutable $date,
        ?DocumentType $type = null,
    ) {
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
        $builder = new BaseDocumentBuilder();
        $document = $builder->createBaseDocument('Cancelacion', $this->documentType->xmlNamespaceCancellation());
        $cancelacion = $this->xmlDocumentElement($document);
        $cancelacion->setAttribute('RfcEmisor', $this->rfc()); // en el anexo 20 es opcional!
        $cancelacion->setAttribute('Fecha', $this->date()->format('Y-m-d\TH:i:s'));

        foreach ($this->documents as $cancelDocument) {
            $folios = $document->createElement('Folios');
            $cancelacion->appendChild($folios);
            $folio = $document->createElement('Folio');
            $folios->appendChild($folio);
            $folio->setAttribute('UUID', (string) $cancelDocument->uuid());
            $folio->setAttribute('Motivo', (string) $cancelDocument->reason());
            if ($cancelDocument->hasSubstituteOf()) {
                $folio->setAttribute('FolioSustitucion', (string) $cancelDocument->substituteOf());
            }
        }

        return $document;
    }

    public function belongsToRfc(string $rfc): bool
    {
        return $rfc === $this->rfc();
    }
}
