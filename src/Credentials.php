<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion;

class Credentials
{
    /** @var string */
    private $certificate;

    /** @var string */
    private $privateKey;

    /** @var string */
    private $passPhrase;

    public function __construct(string $certificate, string $privateKey, string $passPhrase)
    {
        $this->certificate = $certificate;
        $this->privateKey = $privateKey;
        $this->passPhrase = $passPhrase;
    }

    public function certificate(): string
    {
        return $this->certificate;
    }

    public function privateKey(): string
    {
        return $this->privateKey;
    }

    public function passPhrase(): string
    {
        return $this->passPhrase;
    }
}
