<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion;

use PhpCfdi\Credentials\Credential;
use PhpCfdi\XmlCancelacion\Exceptions\CannotLoadCertificateAndPrivateKey;
use PhpCfdi\XmlCancelacion\Exceptions\CertificateIsNotCSD;
use Throwable;

class Credentials
{
    private ?Credential $csd = null;

    public function __construct(
        private readonly string $certificate,
        private readonly string $privateKey,
        private readonly string $passPhrase
    ) {
    }

    public static function createWithPhpCfdiCredential(Credential $credential): self
    {
        $new = new self('', '', '');
        $new->setCsd($credential);
        return $new;
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

    /** @return array<mixed> */
    public function publicKeyData(): array
    {
        return $this->getCsd()->certificate()->publicKey()->parsed();
    }

    public function rfc(): string
    {
        return $this->getCsd()->rfc();
    }

    /** @throws CannotLoadCertificateAndPrivateKey */
    protected function makePhpCfdiCredential(): Credential
    {
        try {
            return Credential::openFiles($this->certificate(), $this->privateKey(), $this->passPhrase());
        } catch (Throwable $exception) {
            throw new CannotLoadCertificateAndPrivateKey(
                $this->certificate(),
                $this->privateKey(),
                $this->passPhrase(),
                $exception
            );
        }
    }

    protected function getCsd(): Credential
    {
        if (null === $this->csd) {
            $credential = $this->makePhpCfdiCredential();
            $this->setCsd($credential);
            return $credential;
        }
        return $this->csd;
    }

    /** @throws CertificateIsNotCSD */
    protected function setCsd(Credential $credential): void
    {
        if (! $credential->isCsd()) {
            throw new CertificateIsNotCSD($credential->certificate()->serialNumber()->bytes());
        }
        $this->csd = $credential;
    }
}
