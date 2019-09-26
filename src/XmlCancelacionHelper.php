<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion;

use DateTimeImmutable;
use PhpCfdi\XmlCancelacion\Cancellation\CancellationCapsule;
use PhpCfdi\XmlCancelacion\CancellationAnswer\CancellationAnswerCapsule;
use PhpCfdi\XmlCancelacion\Contracts\CapsuleInterface;
use PhpCfdi\XmlCancelacion\Contracts\SignerInterface;
use PhpCfdi\XmlCancelacion\Definitions\CancellationAnswer;
use PhpCfdi\XmlCancelacion\Definitions\RfcRole;
use PhpCfdi\XmlCancelacion\Exceptions\HelperDoesNotHaveCredentials;
use PhpCfdi\XmlCancelacion\ObtainRelated\ObtainRelatedCapsule;

class XmlCancelacionHelper
{
    /** @var Credentials|null */
    private $credentials;

    /** @var SignerInterface */
    private $signer;

    /**
     * Helper object to create xml signed documents ready to send to PAC/SAT
     *
     * @param Credentials|null $credentials
     * @param SignerInterface|null $signer
     */
    public function __construct(?Credentials $credentials = null, ?SignerInterface $signer = null)
    {
        $this->credentials = $credentials;
        $this->signer = $signer ?? new DOMSigner();
    }

    public function hasCredentials(): bool
    {
        return (null !== $this->credentials);
    }

    public function getCredentials(): Credentials
    {
        if (null === $this->credentials) {
            throw new HelperDoesNotHaveCredentials();
        }
        return $this->credentials;
    }

    public function setCredentials(Credentials $credentials): self
    {
        $this->credentials = $credentials;
        return $this;
    }

    public function getSigner(): SignerInterface
    {
        return $this->signer;
    }

    public function setSigner(SignerInterface $signer): self
    {
        $this->signer = $signer;
        return $this;
    }

    public function setNewCredentials(string $certificateFile, string $privateKeyFile, string $passPhrase): self
    {
        $credentials = new Credentials($certificateFile, $privateKeyFile, $passPhrase);
        return $this->setCredentials($credentials);
    }

    public function createDateTime(?DateTimeImmutable $dateTime): DateTimeImmutable
    {
        if (null === $dateTime) {
            return new DateTimeImmutable();
        }
        return $dateTime;
    }

    public function signCancellation(string $uuid, ?DateTimeImmutable $dateTime = null): string
    {
        $capsule = new CancellationCapsule($this->getCredentials()->rfc(), [$uuid], $this->createDateTime($dateTime));
        return $this->signCapsule($capsule);
    }

    public function signCancellationUuids(array $uuids, ?DateTimeImmutable $dateTime = null): string
    {
        $capsule = new CancellationCapsule($this->getCredentials()->rfc(), $uuids, $this->createDateTime($dateTime));
        return $this->signCapsule($capsule);
    }

    public function signObtainRelated(string $uuid, RfcRole $role, string $pacRfc): string
    {
        $capsule = new ObtainRelatedCapsule($uuid, $this->getCredentials()->rfc(), $role, $pacRfc);
        return $this->signCapsule($capsule);
    }

    public function signCancellationAnswer(
        string $uuid,
        CancellationAnswer $answer,
        string $pacRfc,
        DateTimeImmutable $dateTime = null
    ): string {
        $rfc = $this->getCredentials()->rfc();
        $dateTime = $this->createDateTime($dateTime);
        $capsule = new CancellationAnswerCapsule($rfc, $uuid, $answer, $pacRfc, $dateTime);
        return $this->signCapsule($capsule);
    }

    public function signCapsule(CapsuleInterface $capsule): string
    {
        $credentials = $this->getCredentials();
        $signer = $this->getSigner();
        return $signer->signCapsule($capsule, $credentials);
    }
}
