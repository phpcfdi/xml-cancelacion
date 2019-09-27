<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Cancellation;

use DOMDocument;
use DOMElement;
use PhpCfdi\XmlCancelacion\Contracts\BaseDocumentBuilder;

class CancellationDocumentBuilder
{
    public function makeDocument(CancellationCapsule $capsule): DOMDocument
    {
        $document = (new BaseDocumentBuilder())->createBaseDocument('Cancelacion', 'http://cancelacfd.sat.gob.mx');
        /** @var DOMElement $cancelacion */
        $cancelacion = $document->documentElement;

        // registro de atributo RfcEmisor
        $cancelacion->setAttribute('RfcEmisor', $capsule->rfc()); // en el anexo 20 es opcional!
        $cancelacion->setAttribute('Fecha', $capsule->date()->format('Y-m-d\TH:i:s'));
        // creación del nodo folios
        $folios = $cancelacion->appendChild($document->createElement('Folios'));
        // creación del UUID
        foreach ($capsule->uuids() as $uuid) {
            $folios->appendChild($document->createElement('UUID', htmlspecialchars($uuid, ENT_XML1)));
        }
        return $document;
    }
}
