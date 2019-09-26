<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit;

use DateTimeImmutable;
use PhpCfdi\XmlCancelacion\Cancellation\CancellationCapsule;
use PhpCfdi\XmlCancelacion\Credentials;
use PhpCfdi\XmlCancelacion\DOMSigner;
use PhpCfdi\XmlCancelacion\Tests\TestCase;
use PhpCfdi\XmlCancelacion\XmlSecLibsSigner;

/** @covers \PhpCfdi\XmlCancelacion\XmlSecLibsSigner */
class XmlSecLibsSignerTest extends TestCase
{
    public function testsignIsEqualToDomSigner(): void
    {
        $cerContent = $this->filePath('LAN7008173R5.cer.pem');
        $keyContent = $this->filePath('LAN7008173R5.key.pem');
        $passPhrase = trim($this->fileContents('LAN7008173R5.password'));
        $credentials = new Credentials($cerContent, $keyContent, $passPhrase);
        $capsule = new CancellationCapsule(
            'LAN7008173R5',
            ['11111111-2222-3333-4444-000000000001'],
            new DateTimeImmutable('2019-01-13T14:15:16-06:00')
        );

        // create expected using DOMSigner
        $domSigner = new DOMSigner();
        $expected = $domSigner->signCapsule($capsule, $credentials);

        // create expected using XmlSecLibsSigner
        $xmlSecLibsSigner = new XmlSecLibsSigner();
        $xmlsecSignature = $xmlSecLibsSigner->signCapsule($capsule, $credentials);

        $this->assertXmlStringEqualsXmlString($expected, $xmlsecSignature);
    }
}
