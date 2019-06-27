<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion;

use CfdiUtils\Certificado\Certificado;
use CfdiUtils\PemPrivateKey\PemPrivateKey;
use CfdiUtils\Utils\Xml;
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
        $signature->appendChild(
            $document->importNode($this->createKeyInfo($signObjects->certificate()), true)
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

    protected function createKeyInfo(string $certificateFile): DOMElement
    {
        $certificate = new Certificado($certificateFile);
        $issuerName = $certificate->getCertificateName();
        $serialNumber = $certificate->getSerialObject()->asAscii();
        $pemContents = $certificate->getPemContents();
        return $this->createKeyInfoWithData($issuerName, $serialNumber, $pemContents);
    }

    protected function createKeyInfoWithData(string $issuerName, string $serialNumber, string $pemContents): DOMElement
    {
        $document = $this->document;
        $keyInfo = $document->createElement('KeyInfo');
        $x509Data = $document->createElement('X509Data');
        $x509IssuerSerial = $document->createElement('X509IssuerSerial');
        $x509IssuerSerial->appendChild(
            Xml::createElement($document, 'X509IssuerName', $issuerName)
        );
        $x509IssuerSerial->appendChild(
            Xml::createElement($document, 'X509SerialNumber', $serialNumber)
        );
        $x509Data->appendChild($x509IssuerSerial);

        $certificateContents = implode('', preg_grep('/^((?!-).)*$/', explode(PHP_EOL, $pemContents)));
        $x509Certificate = Xml::createElement($document, 'X509Certificate', $certificateContents);
        $x509Data->appendChild($x509Certificate);

        $keyInfo->appendChild($x509Data);

        $keyInfo->appendChild($this->createKeyValueFromPemContents($pemContents));
        return $keyInfo;
    }

    protected function createKeyValue(string $certificateFile): DOMElement
    {
        $certificate = new Certificado($certificateFile);
        return $this->createKeyValueFromCertificado($certificate);
    }

    protected function createKeyValueFromCertificado(Certificado $certificate): DOMElement
    {
        return $this->createKeyValueFromPemContents($certificate->getPemContents());
    }

    protected function createKeyValueFromPemContents(string $pemContents): DOMElement
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
