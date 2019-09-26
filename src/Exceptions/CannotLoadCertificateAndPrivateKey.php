<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Exceptions;

use Throwable;

class CannotLoadCertificateAndPrivateKey extends XmlCancelacionRuntimeException
{
    /** @var string */
    private $certificateFile;

    /** @var string */
    private $privateKeyFile;

    /** @var string */
    private $passPhrase;

    public function __construct(string $certificate, string $privateKey, string $passPhrase, Throwable $previous)
    {
        parent::__construct('Cannot load certificate and private key', 0, $previous);
        $this->certificateFile = $certificate;
        $this->privateKeyFile = $privateKey;
        $this->passPhrase = $passPhrase;
    }

    public function getCertificateFile(): string
    {
        return $this->certificateFile;
    }

    public function getPrivateKeyFile(): string
    {
        return $this->privateKeyFile;
    }

    public function getPassPhrase(): string
    {
        return $this->passPhrase;
    }
}
