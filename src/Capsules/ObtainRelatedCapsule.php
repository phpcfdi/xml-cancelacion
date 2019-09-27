<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Capsules;

use DOMDocument;
use DOMElement;
use PhpCfdi\XmlCancelacion\Definitions;
use PhpCfdi\XmlCancelacion\Definitions\RfcRole;

class ObtainRelatedCapsule implements CapsuleInterface
{
    /** @var string */
    private $uuid;

    /** @var string */
    private $rfc;

    /** @var Definitions\RfcRole */
    private $role;

    /** @var string */
    private $pacRfc;

    public function __construct(string $uuid, string $rfc, RfcRole $role, string $pacRfc)
    {
        $this->uuid = $uuid;
        $this->rfc = $rfc;
        $this->role = $role;
        $this->pacRfc = $pacRfc;
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function rfc(): string
    {
        return $this->rfc;
    }

    public function role(): Definitions\RfcRole
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
            ->createBaseDocument('PeticionConsultaRelacionados', 'http://cancelacfd.sat.gob.mx');

        /** @var DOMElement $peticion */
        $peticion = $document->documentElement;
        $peticion->setAttribute('RfcEmisor', ($this->role()->isIssuer()) ? $this->rfc() : '');
        $peticion->setAttribute('RfcPacEnviaSolicitud', $this->pacRfc());
        $peticion->setAttribute('RfcReceptor', ($this->role()->isReceiver()) ? $this->rfc() : '');
        $peticion->setAttribute('Uuid', $this->uuid());

        return $document;
    }

    public function belongsToRfc(string $rfc): bool
    {
        return ($rfc === $this->rfc());
    }
}
