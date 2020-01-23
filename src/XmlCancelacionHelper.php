<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion;

use DateTimeImmutable;
use PhpCfdi\XmlCancelacion\Capsules\Cancellation;
use PhpCfdi\XmlCancelacion\Capsules\CancellationAnswer;
use PhpCfdi\XmlCancelacion\Capsules\CapsuleInterface;
use PhpCfdi\XmlCancelacion\Capsules\ObtainRelated;
use PhpCfdi\XmlCancelacion\Definitions\CancelAnswer;
use PhpCfdi\XmlCancelacion\Definitions\DocumentType;
use PhpCfdi\XmlCancelacion\Definitions\RfcRole;
use PhpCfdi\XmlCancelacion\Exceptions\HelperDoesNotHaveCredentials;
use PhpCfdi\XmlCancelacion\Signers\DOMSigner;
use PhpCfdi\XmlCancelacion\Signers\SignerInterface;

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

    /**
     * Creates a XML Signed cancellation request for a single CFDI
     *
     * @param string $uuid
     * @param DateTimeImmutable|null $dateTime
     * @return string
     */
    public function signCancellation(string $uuid, ?DateTimeImmutable $dateTime = null): string
    {
        $capsule = $this->createCancellationObject([$uuid], $dateTime, DocumentType::cfdi());
        return $this->signCapsule($capsule);
    }

    /**
     * Creates a XML Signed cancellation request for one or multiple CFDI
     * Is adviced to always send a request for only 1 UUID
     *
     * @param string[] $uuids
     * @param DateTimeImmutable|null $dateTime
     * @return string
     */
    public function signCancellationUuids(array $uuids, ?DateTimeImmutable $dateTime = null): string
    {
        $capsule = $this->createCancellationObject($uuids, $dateTime, DocumentType::cfdi());
        return $this->signCapsule($capsule);
    }

    /**
     * Creates a XML Signed cancellation request for a single Retention
     * Is adviced to always send a request for only 1 UUID
     *
     * @param string $uuid
     * @param DateTimeImmutable|null $dateTime
     * @return string
     */
    public function signRetentionCancellation(string $uuid, ?DateTimeImmutable $dateTime = null): string
    {
        $capsule = $this->createCancellationObject([$uuid], $dateTime, DocumentType::retention());
        return $this->signCapsule($capsule);
    }

    /**
     * Creates a XML Signed cancellation request for one or multiple Retention
     * Is adviced to always send a request for only 1 UUID
     *
     * @param string[] $uuids
     * @param DateTimeImmutable|null $dateTime
     * @return string
     */
    public function signRetentionCancellationUuids(array $uuids, ?DateTimeImmutable $dateTime = null): string
    {
        $capsule = $this->createCancellationObject($uuids, $dateTime, DocumentType::retention());
        return $this->signCapsule($capsule);
    }

    /**
     * Creates a XML Signed request to obtain the related CFDI documents to a single UUID
     *
     * @param string $uuid
     * @param RfcRole $role
     * @param string $pacRfc
     * @return string
     */
    public function signObtainRelated(string $uuid, RfcRole $role, string $pacRfc): string
    {
        $capsule = new ObtainRelated($uuid, $this->getCredentials()->rfc(), $role, $pacRfc);
        return $this->signCapsule($capsule);
    }

    /**
     * Creates a XML Signed cancellation answer (accept or reject a cancellation)

     * @param string $uuid
     * @param CancelAnswer $answer
     * @param string $pacRfc
     * @param DateTimeImmutable|null $dateTime
     * @return string
     */
    public function signCancellationAnswer(
        string $uuid,
        CancelAnswer $answer,
        string $pacRfc,
        DateTimeImmutable $dateTime = null
    ): string {
        $rfc = $this->getCredentials()->rfc();
        $dateTime = $this->createDateTime($dateTime);
        $capsule = new CancellationAnswer($rfc, $uuid, $answer, $pacRfc, $dateTime);
        return $this->signCapsule($capsule);
    }

    public function signCapsule(CapsuleInterface $capsule): string
    {
        $credentials = $this->getCredentials();
        $signer = $this->getSigner();
        return $signer->signCapsule($capsule, $credentials);
    }

    /**
     * This method was isolated to be able to test the calls made by specific methods
     * It is not intended to be replaced or overrided by anything but tests
     *
     * @param string[] $uuids
     * @param DateTimeImmutable|null $dateTime
     * @param DocumentType $type
     * @return Cancellation
     */
    protected function createCancellationObject(
        array $uuids,
        ?DateTimeImmutable $dateTime,
        DocumentType $type
    ): Cancellation {
        return new Cancellation($this->getCredentials()->rfc(), $uuids, $this->createDateTime($dateTime), $type);
    }
}
