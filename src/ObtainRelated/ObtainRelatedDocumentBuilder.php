<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\ObtainRelated;

use DOMDocument;
use DOMElement;
use PhpCfdi\XmlCancelacion\Contracts\AbstractCapsuleDocumentBuilder;
use PhpCfdi\XmlCancelacion\Contracts\CapsuleInterface;
use PhpCfdi\XmlCancelacion\Exceptions\InvalidCapsuleType;

class ObtainRelatedDocumentBuilder extends AbstractCapsuleDocumentBuilder
{
    /**
     * Build and return a DOMDocument with the capsule data
     *
     * @param CapsuleInterface|ObtainRelatedCapsule $capsule
     * @return DOMDocument
     */
    public function makeDocument(CapsuleInterface $capsule): DOMDocument
    {
        if (! $capsule instanceof ObtainRelatedCapsule) {
            throw new InvalidCapsuleType($capsule, ObtainRelatedCapsule::class);
        }

        $document = $this->createBaseDocument('PeticionConsultaRelacionados', 'http://cancelacfd.sat.gob.mx');
        /** @var DOMElement $peticion */
        $peticion = $document->documentElement;
        $peticion->setAttribute('RfcEmisor', ($capsule->role()->isIssuer()) ? $capsule->rfc() : '');
        $peticion->setAttribute('RfcPacEnviaSolicitud', $capsule->pacRfc());
        $peticion->setAttribute('RfcReceptor', ($capsule->role()->isReceiver()) ? $capsule->rfc() : '');
        $peticion->setAttribute('Uuid', $capsule->uuid());

        return $document;
    }
}
