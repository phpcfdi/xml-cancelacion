<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\System;

use PhpCfdi\XmlCancelacion\Capsules\ObtainRelated;
use PhpCfdi\XmlCancelacion\Credentials;
use PhpCfdi\XmlCancelacion\Models\RfcRole;
use PhpCfdi\XmlCancelacion\Signers\DOMSigner;
use PhpCfdi\XmlCancelacion\Tests\TestCase;

class ObtainRelatedSignatureTest extends TestCase
{
    public function testMakeSignatureAgainstKnownFile(): void
    {
        $cerFile = $this->filePath('LAN7008173R5.cer.pem');
        $keyFile = $this->filePath('LAN7008173R5.key.pem');
        $passPhrase = trim($this->fileContents('LAN7008173R5.password'));
        $credentials = new Credentials($cerFile, $keyFile, $passPhrase);

        $capsule = new ObtainRelated(
            '11111111-2222-3333-4444-000000000001',
            'LAN7008173R5',
            RfcRole::receiver(),
            'CVD110412TF6'
        );
        $signer = new DOMSigner();
        $document = $capsule->exportToDocument();
        $signer->signDocument($document, $credentials);
        $signature = $document->saveXML();

        $expectedXml = $this->xmlWithoutWhitespace($this->fileContents('obtain-related-signed.xml'));
        $this->assertSame($expectedXml, $signature);
    }
}
