<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Capsules;

use DateTimeImmutable;
use DOMDocument;
use PhpCfdi\XmlCancelacion\Internal\XmlHelperFunctions;
use PhpCfdi\XmlCancelacion\Models\CancelAnswer;
use PhpCfdi\XmlCancelacion\Models\DocumentType;

class CancellationAnswer implements CapsuleInterface
{
    use XmlHelperFunctions;

    public function __construct(
        private readonly string $rfc,
        private readonly string $uuid,
        private readonly CancelAnswer $answer,
        private readonly string $pacRfc,
        private readonly DateTimeImmutable $dateTime,
    ) {
    }

    public function rfc(): string
    {
        return $this->rfc;
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function answer(): CancelAnswer
    {
        return $this->answer;
    }

    public function dateTime(): DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function pacRfc(): string
    {
        return $this->pacRfc;
    }

    public function exportToDocument(): DOMDocument
    {
        $document = (new BaseDocumentBuilder())
            ->createBaseDocument('SolicitudAceptacionRechazo', DocumentType::cfdi()->value());

        $solicitudAceptacionRechazo = $this->xmlDocumentElement($document);
        $solicitudAceptacionRechazo->setAttribute('Fecha', $this->dateTime()->format('Y-m-d\TH:i:s'));
        $solicitudAceptacionRechazo->setAttribute('RfcPacEnviaSolicitud', $this->pacRfc());
        $solicitudAceptacionRechazo->setAttribute('RfcReceptor', $this->rfc());
        $solicitudAceptacionRechazo->appendChild(
            $folios = $document->createElement('Folios')
        );
        $folios->appendChild(
            $document->createElement('UUID', htmlspecialchars($this->uuid(), ENT_XML1))
        );
        $folios->appendChild(
            $document->createElement('Respuesta', htmlspecialchars($this->answer()->value(), ENT_XML1))
        );

        return $document;
    }

    public function belongsToRfc(string $rfc): bool
    {
        return $rfc === $this->rfc();
    }
}
