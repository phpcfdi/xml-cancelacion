<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Capsules;

use DOMDocument;
use PhpCfdi\XmlCancelacion\Internal\XmlHelperFunctions;
use PhpCfdi\XmlCancelacion\Models\DocumentType;
use PhpCfdi\XmlCancelacion\Models\RfcRole;

class ObtainRelated implements CapsuleInterface
{
    use XmlHelperFunctions;

    public function __construct(
        private readonly string $uuid,
        private readonly string $rfc,
        private readonly RfcRole $role,
        private readonly string $pacRfc
    ) {
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function rfc(): string
    {
        return $this->rfc;
    }

    public function role(): RfcRole
    {
        return $this->role;
    }

    public function pacRfc(): string
    {
        return $this->pacRfc;
    }

    public function exportToDocument(): DOMDocument
    {
        $document = (new BaseDocumentBuilder())
            ->createBaseDocument('PeticionConsultaRelacionados', DocumentType::cfdi()->value());

        $peticion = $this->xmlDocumentElement($document);
        $peticion->setAttribute('RfcEmisor', ($this->role()->isIssuer()) ? $this->rfc() : '');
        $peticion->setAttribute('RfcPacEnviaSolicitud', $this->pacRfc());
        $peticion->setAttribute('RfcReceptor', ($this->role()->isReceiver()) ? $this->rfc() : '');
        $peticion->setAttribute('Uuid', $this->uuid());

        return $document;
    }

    public function belongsToRfc(string $rfc): bool
    {
        return $rfc === $this->rfc();
    }
}
