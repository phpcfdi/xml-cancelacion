<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\ObtainRelated;

use DOMDocument;
use PhpCfdi\XmlCancelacion\Contracts\CapsuleInterface;
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
        return (new ObtainRelatedDocumentBuilder())->makeDocument($this);
    }

    public function belongsToRfc(string $rfc): bool
    {
        return ($rfc === $this->rfc());
    }
}
