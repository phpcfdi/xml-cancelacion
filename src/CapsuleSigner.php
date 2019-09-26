<?php

/** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion;

use DOMDocument;
use PhpCfdi\XmlCancelacion\Contracts\AbstractCapsuleDocumentBuilder;
use PhpCfdi\XmlCancelacion\Contracts\CapsuleInterface;
use PhpCfdi\XmlCancelacion\Exceptions\CapsuleRfcDoesnotBelongToCertificateRfc;

/** @deprecated 0.5.0 */
class CapsuleSigner extends AbstractCapsuleDocumentBuilder
{
    public function __construct(array $extraNamespaces = null)
    {
        parent::__construct($extraNamespaces);
        trigger_error('Deprecated class since 0.5.0', E_USER_DEPRECATED);
    }

    /**
     * @param Capsule $capsule
     * @param Credentials $signObjects
     * @return string
     * @throws CapsuleRfcDoesnotBelongToCertificateRfc
     * @deprecated 0.5.0
     */
    public function sign(Capsule $capsule, Credentials $signObjects): string
    {
        trigger_error('Deprecated method since 0.5.0', E_USER_DEPRECATED);
        return (new DOMSigner())->signCapsule($capsule, $signObjects);
    }

    /**
     * @param Capsule $capsule
     * @return DOMDocument
     * @deprecated 0.5.0
     */
    public function createDocument(Capsule $capsule): DOMDocument
    {
        trigger_error('Deprecated method since 0.5.0', E_USER_DEPRECATED);
        return $capsule->exportToDocument();
    }

    /**
     * @param CapsuleInterface&Capsule $capsule
     * @return DOMDocument
     * @deprecated 0.5.0
     */
    public function makeDocument(CapsuleInterface $capsule): DOMDocument
    {
        trigger_error('Deprecated method since 0.5.0', E_USER_DEPRECATED);
        $this->assertCapsuleType($capsule, Capsule::class);
        return $capsule->exportToDocument();
    }
}
