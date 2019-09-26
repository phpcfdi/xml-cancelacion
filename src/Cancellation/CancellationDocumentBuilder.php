<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Cancellation;

use DOMDocument;
use DOMElement;
use PhpCfdi\XmlCancelacion\Contracts\AbstractCapsuleDocumentBuilder;
use PhpCfdi\XmlCancelacion\Contracts\CapsuleInterface;

class CancellationDocumentBuilder extends AbstractCapsuleDocumentBuilder
{
    /**
     * Build and return a DOMDocument with the capsule data
     *
     * @param CapsuleInterface&CancellationCapsule $capsule
     * @return DOMDocument
     */
    public function makeDocument(CapsuleInterface $capsule): DOMDocument
    {
        $this->assertCapsuleType($capsule, CancellationCapsule::class);

        $document = $this->createBaseDocument('Cancelacion', 'http://cancelacfd.sat.gob.mx');
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
