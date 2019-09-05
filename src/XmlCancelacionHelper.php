<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion;

use DateTimeImmutable;

class XmlCancelacionHelper
{
    /** @var Credentials|null */
    private $credentials;

    /**
     * XmlCancelacionHelper constructor.
     * @param Credentials|null $credentials
     */
    public function __construct(?Credentials $credentials = null)
    {
        $this->credentials = $credentials;
    }

    public function hasCredentials(): bool
    {
        return (null !== $this->credentials);
    }

    public function getCredentials(): Credentials
    {
        if (null === $this->credentials) {
            throw new \LogicException('The object has no credentials');
        }
        return $this->credentials;
    }

    public function setCredentials(Credentials $credentials): self
    {
        $this->credentials = $credentials;
        return $this;
    }

    public function setNewCredentials(string $certificate, string $privateKey, string $passPhrase): self
    {
        $credentials = new Credentials($certificate, $privateKey, $passPhrase);
        return $this->setCredentials($credentials);
    }

    public function make(string $uuid, ? DateTimeImmutable $dateTime = null): string
    {
        return $this->makeUuids([$uuid], $dateTime);
    }

    public function makeUuids(array $uuids, ? DateTimeImmutable $dateTime = null): string
    {
        $dateTime = $dateTime ?? new DateTimeImmutable();
        $credentials = $this->getCredentials();
        $capsule = $this->createCapsule($credentials->rfc(), $uuids, $dateTime);
        $signer = $this->createCapsuleSigner();
        return $signer->sign($capsule, $credentials);
    }

    protected function createCapsule(string $rfc, array $uuids, DateTimeImmutable $dateTime): Capsule
    {
        return new Capsule($rfc, $uuids, $dateTime);
    }

    protected function createCapsuleSigner(): CapsuleSigner
    {
        return new CapsuleSigner();
    }
}
