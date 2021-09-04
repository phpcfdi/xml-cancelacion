<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Signers;

use DOMDocument;
use DOMElement;
use PhpCfdi\XmlCancelacion\Credentials;
use PhpCfdi\XmlCancelacion\Internal\XmlHelperFunctions;

class DOMSigner implements SignerInterface
{
    use CreateKeyInfoElementTrait;

    use SignCapsuleMethodTrait;

    use XmlHelperFunctions;

    /** @var string */
    private $digestSource = '';

    /** @var string */
    private $digestValue = '';

    /** @var string */
    private $signedInfoSource = '';

    /** @var string */
    private $signedInfoValue = '';

    public function getDigestSource(): string
    {
        return $this->digestSource;
    }

    public function getDigestValue(): string
    {
        return $this->digestValue;
    }

    public function getSignedInfoSource(): string
    {
        return $this->signedInfoSource;
    }

    public function getSignedInfoValue(): string
    {
        return $this->signedInfoValue;
    }

    public function signDocument(DOMDocument $document, Credentials $credentials): void
    {
        // Setup digestSource & digestValue
        // C14N: no exclusive, no comments (if exclusive will drop not used namespaces)
        $this->digestSource = $document->C14N(false, false);
        $this->digestValue = base64_encode(sha1($this->digestSource, true));

        /** @var DOMElement $signature */
        $signature = $document->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'Signature');
        $this->xmlDocumentElement($document)->appendChild($signature);

        // SignedInfo: import in document and append to the Signature element
        $signedInfo = $signature->appendChild(
            $document->importNode($this->createSignedInfoElement(), true)
        );

        // need to append signature to document and signed info **before** C14N
        // otherwise the signedinfo will not contain namespaces
        // C14N: no exclusive, no comments (if exclusive will drop not used namespaces)
        $this->signedInfoSource = $signedInfo->C14N(false, false);
        $this->signedInfoValue = base64_encode($credentials->sign($this->signedInfoSource, OPENSSL_ALGO_SHA1));

        // SIGNATUREVALUE
        $signature->appendChild(
            $document->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'SignatureValue', $this->signedInfoValue)
        );

        // KEYINFO
        $keyInfoElement = $this->createKeyInfoElement(
            $document,
            $credentials->certificateIssuerName(),
            $credentials->serialNumber(),
            $credentials->certificateAsPEM(),
            $credentials->publicKeyData()
        );
        $signature->appendChild($document->importNode($keyInfoElement, true));
    }

    protected function createSignedInfoElement(): DOMElement
    {
        $template = '<SignedInfo>
              <CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315"/>
              <SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1"/>
              <Reference URI="">
                <Transforms>
                  <Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"/>
                </Transforms>
                <DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1"/>
                <DigestValue>' . $this->getDigestValue() . '</DigestValue>
              </Reference>
            </SignedInfo>';

        $docInfo = new DOMDocument();
        $docInfo->preserveWhiteSpace = false;
        $docInfo->formatOutput = false;
        $docInfo->loadXML($template);
        return $this->xmlDocumentElement($docInfo);
    }
}
