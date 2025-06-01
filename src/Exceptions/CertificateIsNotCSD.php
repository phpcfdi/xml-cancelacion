<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Exceptions;

class CertificateIsNotCSD extends XmlCancelacionRuntimeException
{
    public function __construct(private readonly string $serialNumber)
    {
        parent::__construct(sprintf('The certificate [%s] is not a CSD', $this->serialNumber));
    }

    public function getSerialNumber(): string
    {
        return $this->serialNumber;
    }
}
