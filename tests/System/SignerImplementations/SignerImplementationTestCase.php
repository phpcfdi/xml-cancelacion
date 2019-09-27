<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\System\SignerImplementations;

use DateTimeImmutable;
use DOMDocument;
use PhpCfdi\XmlCancelacion\Capsules\CapsuleInterface;
use PhpCfdi\XmlCancelacion\Credentials;
use PhpCfdi\XmlCancelacion\Definitions\CancelAnswer;
use PhpCfdi\XmlCancelacion\Definitions\RfcRole;
use PhpCfdi\XmlCancelacion\Exceptions\CapsuleRfcDoesnotBelongToCertificateRfc;
use PhpCfdi\XmlCancelacion\Exceptions\DocumentWithoutRootElement;
use PhpCfdi\XmlCancelacion\Signers\SignerInterface;
use PhpCfdi\XmlCancelacion\Tests\TestCase;
use PhpCfdi\XmlCancelacion\XmlCancelacionHelper;
use PHPUnit\Framework\MockObject\MockObject;
use RobRichards\XMLSecLibs\XMLSecEnc;
use RobRichards\XMLSecLibs\XMLSecurityDSig;

abstract class SignerImplementationTestCase extends TestCase
{
    abstract public function createSigner(): SignerInterface;

    public function testThrowExceptionWhenPassingAnEmptyDomDocument(): void
    {
        /** @var Credentials&MockObject $credentials */
        $credentials = $this->createMock(Credentials::class);
        $signer = $this->createSigner();

        $this->expectException(DocumentWithoutRootElement::class);
        $signer->signDocument(new DOMDocument(), $credentials);
    }

    public function testSignUsingNotMatchingCapsuleRfcAndCredentialsRfc(): void
    {
        /** @var CapsuleInterface&MockObject $capsule */
        $capsule = $this->createMock(CapsuleInterface::class);
        $capsule->method('belongsToRfc')->willReturn(false);
        /** @var Credentials&MockObject $credentials */
        $credentials = $this->createMock(Credentials::class);
        $credentials->method('rfc')->willReturn('LAN7008173R5');

        $signer = $this->createSigner();
        $this->expectException(CapsuleRfcDoesnotBelongToCertificateRfc::class);
        $signer->signCapsule($capsule, $credentials);
    }

    public function createHelper(): XmlCancelacionHelper
    {
        $credentials = new Credentials(
            $this->filePath('LAN7008173R5.cer.pem'),
            $this->filePath('LAN7008173R5.key.pem'),
            trim($this->fileContents('LAN7008173R5.password'))
        );
        $helper = new XmlCancelacionHelper($credentials);
        $helper->setSigner($this->createSigner());
        return $helper;
    }

    public function testCancellation(): void
    {
        $helper = $this->createHelper();
        $signature = $helper->signCancellation(
            'E174F807-BEFA-4CF6-9B11-2A013B12F398',
            new DateTimeImmutable('2019-04-05 16:29:17')
        );
        $expectedXml = $this->xmlWithoutWhitespace($this->fileContents('cancellation-signed.xml'));
        $this->assertXmlStringEqualsXmlString($expectedXml, $signature);
        $this->assertSame($expectedXml, $signature);
        $this->checkSignatureIsValidUsingXmlSecLib($signature);
    }

    public function testObtainRelated(): void
    {
        $helper = $this->createHelper();
        $signature = $helper->signObtainRelated(
            '11111111-2222-3333-4444-000000000001',
            RfcRole::receiver(),
            'CVD110412TF6'
        );
        $expectedXml = $this->xmlWithoutWhitespace($this->fileContents('obtain-related-signed.xml'));
        $this->assertXmlStringEqualsXmlString($expectedXml, $signature);
        $this->assertSame($expectedXml, $signature);
        $this->checkSignatureIsValidUsingXmlSecLib($signature);
    }

    public function testCancellationAnswer(): void
    {
        $helper = $this->createHelper();
        $signature = $helper->signCancellationAnswer(
            '11111111-2222-3333-4444-000000000001',
            CancelAnswer::accept(),
            'CVD110412TF6',
            new DateTimeImmutable('2019-01-13 14:15:16')
        );
        $expectedXml = $this->xmlWithoutWhitespace($this->fileContents('cancellation-answer-signed.xml'));
        $this->assertXmlStringEqualsXmlString($expectedXml, $signature);
        $this->assertSame($expectedXml, $signature);
        $this->checkSignatureIsValidUsingXmlSecLib($signature);
    }

    public function checkSignatureIsValidUsingXmlSecLib(string $signedXml): void
    {
        $document = new DOMDocument();
        $document->loadXML($signedXml);

        $dSig = new XMLSecurityDSig();
        $signature = $dSig->locateSignature($document);
        $this->assertNotNull($signature, 'Cannot locate Signature object');

        // this call **must** be made and before validateReference
        $signedInfo = $dSig->canonicalizeSignedInfo();
        $this->assertNotEmpty($signedInfo, 'Cannot obtain canonicalized SignedInfo');

        $this->assertTrue($dSig->validateReference(), 'Cannot locate referenced object');

        $objKey = $dSig->locateKey();
        if (null === $objKey) {
            $this->fail('Cannot locate XMLSecurityKey object');
            return;
        }

        // must call, otherwise verify will not have the public key to check signature
        $this->assertNotNull(XMLSecEnc::staticLocateKeyInfo($objKey, $signature), 'Cannot extract RSAKeyValue');

        $this->assertSame(1, $dSig->verify($objKey), 'Xml Signature verify fail');
    }
}
