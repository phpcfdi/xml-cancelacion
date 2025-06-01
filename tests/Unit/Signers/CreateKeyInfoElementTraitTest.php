<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit\Signers;

use DOMDocument;
use DOMElement;
use PhpCfdi\XmlCancelacion\Signers\CreateKeyInfoElementTrait;
use PhpCfdi\XmlCancelacion\Tests\TestCase;

/**
 * This test case is specifically created to test ampersands contents when building the XML using
 * \DOMDocument::createElement on CreateKeyInfoElementTrait::createKeyInfoElement.
 *
 * The text contents must be parsed previously as valid XML, and it produces malformed contents when they are not.
 *
 * It is using the local class CreateKeyInfoElementTraitImplementor to be able to expose the createKeyInfoElement
 * method, it is originally created as protected and the implementor class make it public.
 *
 * @see CreateKeyInfoElementTrait::createKeyInfoElement()
 * @see CreateKeyInfoElementTraitImplementor
 */
final class CreateKeyInfoElementTraitTest extends TestCase
{
    private function createKeyInfoElement(string $issuerName, string $serial, string $pemContents): DOMElement
    {
        $signer = new CreateKeyInfoElementTraitImplementor();

        $document = new DOMDocument();
        $pubKeyData = [
            'type' => OPENSSL_KEYTYPE_RSA,
            'rsa' => ['n' => '1', 'e' => '2'],
        ];
        return $signer->createKeyInfoElement($document, $issuerName, $serial, $pemContents, $pubKeyData);
    }

    public function testCreateKeyInfoWithIssuerNameWithAmpersand(): void
    {
        $unparsed = 'Foo & Company';
        $keyInfo = $this->createKeyInfoElement($unparsed, '001', 'XXXX');
        /** @var DOMDocument $document */
        $document = $keyInfo->ownerDocument;

        $this->assertXmlStringEqualsXmlString(
            sprintf('<X509IssuerName>%s</X509IssuerName>', htmlspecialchars($unparsed, ENT_XML1)),
            strval($document->saveXML($keyInfo->getElementsByTagName('X509IssuerName')->item(0))),
            'Ampersand was not correctly parsed on X509IssuerName'
        );
    }

    public function testCreateKeyInfoWithX509SerialNumberWithAmpersand(): void
    {
        $keyInfo = $this->createKeyInfoElement('Foo', $unparsed = '&', 'XXXX');
        /** @var DOMDocument $document */
        $document = $keyInfo->ownerDocument;

        $this->assertXmlStringEqualsXmlString(
            sprintf('<X509SerialNumber>%s</X509SerialNumber>', htmlspecialchars($unparsed, ENT_XML1)),
            strval($document->saveXML($keyInfo->getElementsByTagName('X509SerialNumber')->item(0))),
            'Ampersand was not correctly parsed on X509SerialNumber'
        );
    }

    public function testCreateKeyInfoWithX509CertificateWithAmpersand(): void
    {
        $keyInfo = $this->createKeyInfoElement('Foo', '001', $unparsed = '&');
        /** @var DOMDocument $document */
        $document = $keyInfo->ownerDocument;

        $this->assertXmlStringEqualsXmlString(
            sprintf('<X509Certificate>%s</X509Certificate>', htmlspecialchars($unparsed, ENT_XML1)),
            strval($document->saveXML($keyInfo->getElementsByTagName('X509Certificate')->item(0))),
            'Ampersand was not correctly parsed on X509Certificate'
        );
    }
}
