<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit;

use DOMDocument;
use DOMElement;
use LogicException;
use PhpCfdi\XmlCancelacion\Credentials;
use PhpCfdi\XmlCancelacion\DOMSigner;
use PhpCfdi\XmlCancelacion\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;

class DOMSignerTest extends TestCase
{
    public function testThrowExceptionWhenCannotGetPublicKeyFromCertificate(): void
    {
        $signer = new class(new DOMDocument()) extends DOMSigner {
            public function exposeObtainPublicKeyValues(string $publicKey): array
            {
                return $this->obtainPublicKeyValues($publicKey);
            }
        };

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot read public key from certificate');
        $signer->exposeObtainPublicKeyValues('BAD PUBLIC KEY');
    }

    public function testThrowExceptionWhenPassingAnEmptyDomDocument(): void
    {
        /** @var Credentials&MockObject $credentials */
        $credentials = $this->createMock(Credentials::class);
        $signer = new DOMSigner(new DOMDocument());

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Document does not have a root element');
        $signer->sign($credentials);
    }

    public function testCreateKeyInfoWithIssuerNameWithAmpersand(): void
    {
        $document = new DOMDocument();
        $signer = new class($document) extends DOMSigner {
            public function exposeCreateKeyInfoElement(string $issuerName, string $serialNumber, string $pemContents): DOMElement
            {
                return $this->createKeyInfoElement($issuerName, $serialNumber, $pemContents);
            }

            protected function obtainPublicKeyValues(string $publicKeyContents): array
            {
                return [
                    'type' => OPENSSL_KEYTYPE_RSA,
                    'rsa' => ['n' => '1', 'e' => '2'],
                ];
            }
        };

        $issuerName = 'John & Co';
        $serialNumber = '&0001';
        $pemContents = '&';
        /** @var DOMElement $keyInfo */
        $keyInfo = $signer->exposeCreateKeyInfoElement($issuerName, $serialNumber, $pemContents);

        $this->assertXmlStringEqualsXmlString(
            sprintf('<X509IssuerName>%s</X509IssuerName>', htmlspecialchars($issuerName, ENT_XML1)),
            $document->saveXML($keyInfo->getElementsByTagName('X509IssuerName')[0]),
            'Ampersand was not correctly parsed on X509IssuerName'
        );
        $this->assertXmlStringEqualsXmlString(
            sprintf('<X509SerialNumber>%s</X509SerialNumber>', htmlspecialchars($serialNumber, ENT_XML1)),
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
