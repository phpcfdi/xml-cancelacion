<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\CancellationAnswer;

use DOMDocument;
use DOMElement;
use PhpCfdi\XmlCancelacion\Contracts\BaseDocumentBuilder;

class CancellationAnswerDocumentBuilder
{
    public function makeDocument(CancellationAnswerCapsule $capsule): DOMDocument
    {
        $document = (new BaseDocumentBuilder())
            ->createBaseDocument('SolicitudAceptacionRechazo', 'http://cancelacfd.sat.gob.mx');
        /** @var DOMElement $solicitudAceptacionRechazo */
        $solicitudAceptacionRechazo = $document->documentElement;

        $solicitudAceptacionRechazo->setAttribute('Fecha', $capsule->dateTime()->format('Y-m-d\TH:i:s'));
        $solicitudAceptacionRechazo->setAttribute('RfcPacEnviaSolicitud', $capsule->pacRfc());
        $solicitudAceptacionRechazo->setAttribute('RfcReceptor', $capsule->rfc());
        $solicitudAceptacionRechazo->appendChild(
            $folios = $document->createElement('Folios')
        );
        $folios->appendChild(
            $document->createElement('UUID', htmlspecialchars($capsule->uuid(), ENT_XML1))
        );
        $folios->appendChild(
            $document->createElement('Respuesta', htmlspecialchars($capsule->answer()->value(), ENT_XML1))
        );

        return $document;
    }
}
