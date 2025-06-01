<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Signers;

use DOMDocument;
use PhpCfdi\XmlCancelacion\Capsules\CapsuleInterface;
use PhpCfdi\XmlCancelacion\Credentials;
use PhpCfdi\XmlCancelacion\Exceptions\CapsuleRfcDoesnotBelongToCertificateRfc;

/**
 * The classes that implement this interface must be able to take a DOMDocument
 * and append a signature according to SAT requirements.
 */
interface SignerInterface
{
    /**
     * Sign de capsule and return the generated XML
     *
     * @throws CapsuleRfcDoesnotBelongToCertificateRfc
     */
    public function signCapsule(CapsuleInterface $capsule, Credentials $credentials): string;

    /**
     * Sign the DOMDocument with the specified credentials
     */
    public function signDocument(DOMDocument $document, Credentials $credentials): void;
}
