<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\ObtainRelated;

use DOMDocument;
use DOMElement;
use PhpCfdi\XmlCancelacion\Contracts\BaseDocumentBuilder;

class ObtainRelatedDocumentBuilder
{
    public function makeDocument(ObtainRelatedCapsule $capsule): DOMDocument
    {
        $document = (new BaseDocumentBuilder())
            ->createBaseDocument('PeticionConsultaRelacionados', 'http://cancelacfd.sat.gob.mx');
        /** @var DOMElement $peticion */
        $peticion = $document->documentElement;
        $peticion->setAttribute('RfcEmisor', ($capsule->role()->isIssuer()) ? $capsule->rfc() : '');
        $peticion->setAttribute('RfcPacEnviaSolicitud', $capsule->pacRfc());
        $peticion->setAttribute('RfcReceptor', ($capsule->role()->isReceiver()) ? $capsule->rfc() : '');
        $peticion->setAttribute('Uuid', $capsule->uuid());

        return $document;
    }
}
