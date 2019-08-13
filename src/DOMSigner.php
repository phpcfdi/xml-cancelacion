<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion;

use CfdiUtils\Certificado\Certificado;
use CfdiUtils\PemPrivateKey\PemPrivateKey;
use DOMDocument;
use DOMElement;
use LogicException;
use RuntimeException;

class DOMSigner
{
    /** @var DOMDocument */
    private $document;

    /** @var string */
    private $digestSource = '';

    /** @var string */
    private $digestValue = '';

    /** @var string */
    private $signedInfoSource = '';

    /** @var string */
    private $signedInfoValue = '';

    public function __construct(DOMDocument $document)
    {
        $this->document = $document;
    }

    private function rootElement(DOMDocument $document): DOMElement
    {
        if (null === $document->documentElement) {
            throw new LogicException('Document does not have a root element');
        }
        return $document->documentElement;
    }

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

    public function sign(Credentials $signObjects): void
    {
        $document = $this->document;

        // Setup digestSource & digestValue
        // C14N: no exclusive, no comments (if exclusive will drop not used namespaces)
        $this->digestSource = $document->C14N(false, false);
        $this->digestValue = base64_encode(sha1($this->digestSource, true));

        /** @var DOMElement $signature */
        $signature = $document->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'Signature');
        $this->rootElement($document)->appendChild($signature);

        // append and realocate signedInfo to the node in document
        // SIGNEDINFO
        $signedInfo = $signature->appendChild(
            $document->importNode($this->createSignedInfoElement(), true)
        );

        // need to append signature to document and signed info **before** C14N
        // otherwise the signedinfo will not contain namespaces
        // C14N: no exclusive, no comments (if exclusive will drop not used namespaces)
        $this->signedInfoSource = $signedInfo->C14N(false, false);
        $privateKey = new PemPrivateKey('file://' . $signObjects->privateKey());
        $privateKey->open($signObjects->passPhrase());
        $this->signedInfoValue = base64_encode($privateKey->sign($this->signedInfoSource, OPENSSL_ALGO_SHA1));
        $privateKey->close();

        // SIGNATUREVALUE
        $signature->appendChild(
            $document->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'SignatureValue', $this->signedInfoValue)
        );

        // KEYINFO
        $certificate = new Certificado($signObjects->certificate());
        $issuerName = $certificate->getCertificateName();
        $serialNumber = $certificate->getSerialObject()->asAscii();
        $pemContents = $certificate->getPemContents();
        $signature->appendChild(
            $document->importNode($this->createKeyInfoElement($issuerName, $serialNumber, $pemContents), true)
        );
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
        $docinfoNode = $this->rootElement($docInfo);

        return $docinfoNode;
    }

    protected function createKeyInfoElement(string $issuerName, string $serialNumber, string $pemContents): DOMElement
    {
        $document = $this->document;
        $keyInfo = $document->createElement('KeyInfo');
        $x509Data = $document->createElement('X509Data');
        $x509IssuerSerial = $document->createElement('X509IssuerSerial');
        $x509IssuerSerial->appendChild(
            $document->createElement('X509IssuerName', htmlspecialchars($issuerName, ENT_XML1))
        );
        $x509IssuerSerial->appendChild(
            $document->createElement('X509SerialNumber', htmlspecialchars($serialNumber, ENT_XML1))
        );
        $x509Data->appendChild($x509IssuerSerial);

        $certificateContents = implode('', preg_grep('/^((?!-).)*$/', explode(PHP_EOL, $pemContents)));
        $x509Data->appendChild(
            $document->createElement('X509Certificate', htmlspecialchars($certificateContents, ENT_XML1))
        );

        $keyInfo->appendChild($x509Data);
        $keyInfo->appendChild($this->createKeyValueElement($pemContents));

        return $keyInfo;
    }

    protected function createKeyValueElement(string $pemContents): DOMElement
    {
        $document = $this->document;
        $keyValue = $document->createElement('KeyValue');
        $pubKeyData = $this->obtainPublicKeyValues($pemContents);
        if (OPENSSL_KEYTYPE_RSA === $pubKeyData['type']) {
            $rsaKeyValue = $keyValue->appendChild($document->createElement('RSAKeyValue'));
            $rsaKeyValue->appendChild($document->createElement('Modulus', base64_encode($pubKeyData['rsa']['n'])));
            $rsaKeyValue->appendChild($document->createElement('Exponent', base64_encode($pubKeyData['rsa']['e'])));
        }

        return $keyValue;
    }

    protected function obtainPublicKeyValues(string $publicKeyContents): array
    {
        $pubKey = openssl_get_publickey($publicKeyContents);
        if (! is_resource($pubKey)) {
            throw new RuntimeException('Cannot read public key from certificate');
        }
        $pubKeyData = openssl_pkey_get_details($pubKey) ?: [];
        openssl_free_key($pubKey);

        return $pubKeyData;
    }
}
