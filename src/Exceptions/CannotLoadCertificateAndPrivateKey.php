<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Exceptions;

use Throwable;

class CannotLoadCertificateAndPrivateKey extends XmlCancelacionRuntimeException
{
    public function __construct(
        private readonly string $certificateFile,
        private readonly string $privateKeyFile,
        private readonly string $passPhrase,
        Throwable $previous
    ) {
        parent::__construct('Cannot load certificate and private key', previous: $previous);
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
