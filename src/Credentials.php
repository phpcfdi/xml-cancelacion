<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion;

use PhpCfdi\Credentials\Credential;
use RuntimeException;
use Throwable;

class Credentials
{
    /** @var string */
    private $certificate;

    /** @var string */
    private $privateKey;

    /** @var string */
    private $passPhrase;

    /** @var Credential|null */
    private $csd;

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

    public function sign(string $data, int $algorithm = OPENSSL_ALGO_SHA256): string
    {
        return $this->getCsd()->sign($data, $algorithm);
    }

    public function certificateIssuerName(): string
    {
        return $this->getCsd()->certificate()->issuerAsRfc4514();
    }

    public function serialNumber(): string
    {
        return $this->getCsd()->certificate()->serialNumber()->bytes();
    }

    public function certificateAsPEM(): string
    {
        return $this->getCsd()->certificate()->pem();
    }

    public function publicKeyData(): array
    {
        return $this->getCsd()->certificate()->publicKey()->parsed();
    }

    protected function makePhpCfdiCredential(): Credential
    {
        return Credential::openFiles($this->certificate(), $this->privateKey(), $this->passPhrase());
    }

    protected function getCsd(): Credential
    {
        if (null === $this->csd) {
            try {
                $credential = $this->makePhpCfdiCredential();
                if (! $credential->isCsd()) {
                    throw new RuntimeException('The certificate is not a CSD from SAT');
                }
                $this->csd = $credential;
            } catch (Throwable $error) {
                throw new RuntimeException('Cannot load certificate and private key', 0, $error);
            }
        }
        return $this->csd;
    }

    public function rfc(): string
    {
        return $this->getCsd()->rfc();
    }
}
