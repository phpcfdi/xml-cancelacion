<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit\Signers;

use DateTimeImmutable;
use Exception;
use LogicException;
use PhpCfdi\XmlCancelacion\Capsules\Cancellation;
use PhpCfdi\XmlCancelacion\Credentials;
use PhpCfdi\XmlCancelacion\Models\CancelDocument;
use PhpCfdi\XmlCancelacion\Models\CancelDocuments;
use PhpCfdi\XmlCancelacion\Signers\DOMSigner;
use PhpCfdi\XmlCancelacion\Signers\XmlSecLibsSigner;
use PhpCfdi\XmlCancelacion\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/** @covers \PhpCfdi\XmlCancelacion\Signers\XmlSecLibsSigner */
final class XmlSecLibsSignerTest extends TestCase
{
    public function testsignIsEqualToDomSigner(): void
    {
        $cerContent = $this->filePath('LAN7008173R5.cer.pem');
        $keyContent = $this->filePath('LAN7008173R5.key.pem');
        $passPhrase = trim($this->fileContents('LAN7008173R5.password'));
        $credentials = new Credentials($cerContent, $keyContent, $passPhrase);
        $capsule = new Cancellation(
            'LAN7008173R5',
            new CancelDocuments(CancelDocument::newWithErrorsUnrelated('11111111-2222-3333-4444-000000000001')),
            new DateTimeImmutable('2022-01-13T14:15:16-06:00')
        );

        // create expected using DOMSigner
        $domSigner = new DOMSigner();
        $expected = $domSigner->signCapsule($capsule, $credentials);

        // create expected using XmlSecLibsSigner
        $xmlSecLibsSigner = new XmlSecLibsSigner();
        $xmlsecSignature = $xmlSecLibsSigner->signCapsule($capsule, $credentials);

        $this->assertXmlStringEqualsXmlString($expected, $xmlsecSignature);
    }

    public function testSignDocumentInternalThrowsException(): void
    {
        /** @var Credentials&MockObject $credentials */
        $credentials = $this->createMock(Credentials::class);
        $credentials->method('rfc')->willReturn('LAN7008173R5');
        /** @var XmlSecLibsSigner&MockObject $signer */
        $signer = $this->getMockBuilder(XmlSecLibsSigner::class)
            ->onlyMethods(['signDocumentInternal'])
            ->getMock();
        $signer->method('signDocumentInternal')->willThrowException(new Exception('dummy'));
        $capsule = new Cancellation(
            'LAN7008173R5',
            new CancelDocuments(CancelDocument::newWithErrorsUnrelated('11111111-2222-3333-4444-000000000001')),
            new DateTimeImmutable('2022-01-13T14:15:16-06:00')
        );
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Cannot create signature using XmlSecLibs');
        $signer->signCapsule($capsule, $credentials);
    }
}
