<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Exceptions;

use PhpCfdi\XmlCancelacion\Capsules\CapsuleInterface;

class CapsuleRfcDoesnotBelongToCertificateRfc extends XmlCancelacionRuntimeException
{
    /** @var CapsuleInterface */
    private $capsule;

    /** @var string */
    private $certificateRfc;

    public function __construct(CapsuleInterface $capsule, string $certificateRfc)
    {
        parent::__construct('The capsule RFC does not belong to certificate RFC');
        $this->capsule = $capsule;
        $this->certificateRfc = $certificateRfc;
    }

    public function getCapsule(): CapsuleInterface
    {
        return $this->capsule;
    }

    public function getCertificateRfc(): string
    {
        return $this->certificateRfc;
    }
}
