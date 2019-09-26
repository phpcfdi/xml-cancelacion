<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Exceptions;

class CertificateIsNotCSD extends XmlCancelacionRuntimeException
{
    /** @var string */
    private $serialNumber;

    public function __construct(string $serialNumber)
    {
        parent::__construct(sprintf('The certificate [%s] is not a CSD', $serialNumber));
        $this->serialNumber = $serialNumber;
    }

    public function getSerialNumber(): string
    {
        return $this->serialNumber;
    }
}
