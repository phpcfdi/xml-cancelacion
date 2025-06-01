<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Exceptions;

use PhpCfdi\XmlCancelacion\Capsules\CapsuleInterface;

class CapsuleRfcDoesnotBelongToCertificateRfc extends XmlCancelacionRuntimeException
{
    public function __construct(
        private readonly CapsuleInterface $capsule,
        private readonly string $certificateRfc,
    ) {
        parent::__construct('The capsule RFC does not belong to certificate RFC');
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
