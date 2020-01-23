<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Signers;

use DOMDocument;
use DOMElement;

trait CreateKeyInfoElementTrait
{
    /**
     * @param DOMDocument $document
     * @param string $issuerName
     * @param string $serialNumber
     * @param string $pemContents
     * @param array<mixed> $pubKeyData
     * @return DOMElement
     */
    protected function createKeyInfoElement(
        DOMDocument $document,
        string $issuerName,
        string $serialNumber,
        string $pemContents,
        array $pubKeyData
    ): DOMElement {
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

        $keyInfo = $document->createElement('KeyInfo');
        $keyInfo->appendChild($x509Data);
        $keyInfo->appendChild($this->createKeyValueElement($document, $pubKeyData));

        return $keyInfo;
    }

    /**
     * @param DOMDocument $document
     * @param array<mixed> $pubKeyData
     * @return DOMElement
     */
    private function createKeyValueElement(DOMDocument $document, array $pubKeyData): DOMElement
    {
        $keyValue = $document->createElement('KeyValue');
        $type = $pubKeyData['type'] ?? -1;
        if (OPENSSL_KEYTYPE_RSA === $type) {
            $rsaKeyValue = $keyValue->appendChild($document->createElement('RSAKeyValue'));
            $rsaKeyValue->appendChild($document->createElement('Modulus', base64_encode($pubKeyData['rsa']['n'])));
            $rsaKeyValue->appendChild($document->createElement('Exponent', base64_encode($pubKeyData['rsa']['e'])));
        }

        return $keyValue;
    }
}
