<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit\Signers;

use DOMDocument;
use DOMElement;
use PhpCfdi\XmlCancelacion\Signers\DOMSigner;
use PhpCfdi\XmlCancelacion\Tests\TestCase;

/** @covers \PhpCfdi\XmlCancelacion\Signers\DOMSigner */
class DOMSignerTest extends TestCase
{
    public function testCreateKeyInfoWithIssuerNameWithAmpersand(): void
    {
        $document = new DOMDocument();
        $signer = new class() extends DOMSigner {
            public function exposeCreateKeyInfoElement(
                DOMDocument $document,
                string $name,
                string $serial,
                string $contents,
                array $pubKeyData
            ): DOMElement {
                return $this->createKeyInfoElement($document, $name, $serial, $contents, $pubKeyData);
            }
        };

        $issuerName = 'John & Co';
        $serial = '&0001';
        $pemContents = '&';
        $pubKeyData = [
            'type' => OPENSSL_KEYTYPE_RSA,
            'rsa' => ['n' => '1', 'e' => '2'],
        ];
        /** @var DOMElement $keyInfo */
        $keyInfo = $signer->exposeCreateKeyInfoElement($document, $issuerName, $serial, $pemContents, $pubKeyData);

        $this->assertXmlStringEqualsXmlString(
            sprintf('<X509IssuerName>%s</X509IssuerName>', htmlspecialchars($issuerName, ENT_XML1)),
            $document->saveXML($keyInfo->getElementsByTagName('X509IssuerName')[0]),
            'Ampersand was not correctly parsed on X509IssuerName'
        );
        $this->assertXmlStringEqualsXmlString(
            sprintf('<X509SerialNumber>%s</X509SerialNumber>', htmlspecialchars($serial, ENT_XML1)),
            $document->saveXML($keyInfo->getElementsByTagName('X509SerialNumber')[0]),
            'Ampersand was not correctly parsed on X509SerialNumber'
        );
        $this->assertXmlStringEqualsXmlString(
            sprintf('<X509Certificate>%s</X509Certificate>', htmlspecialchars($pemContents, ENT_XML1)),
            $document->saveXML($keyInfo->getElementsByTagName('X509Certificate')[0]),
            'Ampersand was not correctly parsed on X509Certificate'
        );
    }
}
