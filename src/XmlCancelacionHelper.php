<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion;

use DateTimeImmutable;
use PhpCfdi\XmlCancelacion\Models\CancelDocument;
use PhpCfdi\XmlCancelacion\Models\CancelDocuments;
use PhpCfdi\XmlCancelacion\Capsules\Cancellation;
use PhpCfdi\XmlCancelacion\Capsules\CancellationAnswer;
use PhpCfdi\XmlCancelacion\Capsules\CapsuleInterface;
use PhpCfdi\XmlCancelacion\Capsules\ObtainRelated;
use PhpCfdi\XmlCancelacion\Models\CancelAnswer;
use PhpCfdi\XmlCancelacion\Models\DocumentType;
use PhpCfdi\XmlCancelacion\Models\RfcRole;
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
     * Creates an XML Signed cancellation request for a single CFDI
     *
     * @param CancelDocument $document
     * @param DateTimeImmutable|null $dateTime
     * @return string
     */
    public function signCancellation(CancelDocument $document, ?DateTimeImmutable $dateTime = null): string
    {
        $capsule = $this->createCancellationObject(new CancelDocuments($document), $dateTime, DocumentType::cfdi());
        return $this->signCapsule($capsule);
    }

    /**
     * Creates an XML Signed cancellation request for one or multiple CFDI
     * Is adviced to always send a request for only 1 UUID
     *
     * @param CancelDocuments $documents
     * @param DateTimeImmutable|null $dateTime
     * @return string
     */
    public function signCancellationUuids(CancelDocuments $documents, ?DateTimeImmutable $dateTime = null): string
    {
        $capsule = $this->createCancellationObject($documents, $dateTime, DocumentType::cfdi());
        return $this->signCapsule($capsule);
    }

    /**
     * Creates an XML Signed cancellation request for a single Retention
     * Is adviced to always send a request for only 1 UUID
     *
     * @param CancelDocument $document
     * @param DateTimeImmutable|null $dateTime
     * @return string
     */
    public function signRetentionCancellation(CancelDocument $document, ?DateTimeImmutable $dateTime = null): string
    {
        $documents = new CancelDocuments($document);
        $capsule = $this->createCancellationObject($documents, $dateTime, DocumentType::retention());
        return $this->signCapsule($capsule);
    }

    /**
     * Creates an XML Signed cancellation request for one or multiple Retention
     * Is adviced to always send a request for only 1 UUID
     *
     * @param CancelDocuments $documents
     * @param DateTimeImmutable|null $dateTime
     * @return string
     */
    public function signRetentionCancellationUuids(
        CancelDocuments $documents,
        ?DateTimeImmutable $dateTime = null
    ): string {
        $capsule = $this->createCancellationObject($documents, $dateTime, DocumentType::retention());
        return $this->signCapsule($capsule);
    }

    /**
     * Creates an XML Signed request to obtain the related CFDI documents to a single UUID
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
     * Creates an XML Signed cancellation answer (to accept or reject a cancellation)

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
        $signerInstance = $this->getSigner();
        return $signerInstance->signCapsule($capsule, $this->getCredentials());
    }

    /**
     * This method was isolated to be able to test the calls made by specific methods
     * It is not intended to be replaced or overrided by anything but tests
     *
     * @param CancelDocuments $documents
     * @param DateTimeImmutable|null $dateTime
     * @param DocumentType $type
     * @return Cancellation
     * @internal
     */
    protected function createCancellationObject(
        CancelDocuments $documents,
        ?DateTimeImmutable $dateTime,
        DocumentType $type
    ): Cancellation {
        return new Cancellation($this->getCredentials()->rfc(), $documents, $this->createDateTime($dateTime), $type);
    }
}
