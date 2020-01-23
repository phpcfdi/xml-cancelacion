<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Capsules;

use DateTimeImmutable;
use DOMDocument;
use DOMElement;
use PhpCfdi\XmlCancelacion\Definitions\CancelAnswer;
use PhpCfdi\XmlCancelacion\Definitions\DocumentType;

class CancellationAnswer implements CapsuleInterface
{
    /** @var string */
    private $uuid;

    /** @var CancelAnswer */
    private $answer;

    /** @var string */
    private $rfc;

    /** @var DateTimeImmutable */
    private $dateTime;

    /** @var string */
    private $pacRfc;

    public function __construct(
        string $rfc,
        string $uuid,
        CancelAnswer $answer,
        string $pacRfc,
        DateTimeImmutable $dateTime
    ) {
        $this->rfc = $rfc;
        $this->uuid = $uuid;
        $this->answer = $answer;
        $this->dateTime = $dateTime;
        $this->pacRfc = $pacRfc;
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

        /** @var DOMElement $solicitudAceptacionRechazo */
        $solicitudAceptacionRechazo = $document->documentElement;
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
        return ($rfc === $this->rfc());
    }
}
