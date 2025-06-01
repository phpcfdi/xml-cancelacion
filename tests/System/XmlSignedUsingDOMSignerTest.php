<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\System;

use DateTimeImmutable;
use PhpCfdi\XmlCancelacion\Capsules\Cancellation;
use PhpCfdi\XmlCancelacion\Credentials;
use PhpCfdi\XmlCancelacion\Models\CancelDocument;
use PhpCfdi\XmlCancelacion\Models\CancelDocuments;
use PhpCfdi\XmlCancelacion\Signers\DOMSigner;
use PhpCfdi\XmlCancelacion\Tests\TestCase;

final class XmlSignedUsingDOMSignerTest extends TestCase
{
    private DOMSigner $domSigner;

    public function setUp(): void
    {
        parent::setUp();

        $credentials = new Credentials(
            $this->filePath('EKU9003173C9.cer.pem'),
            $this->filePath('EKU9003173C9.key.pem'),
            trim($this->fileContents('EKU9003173C9.password'))
        );

        $capsule = new Cancellation(
            'EKU9003173C9',
            new CancelDocuments(CancelDocument::newWithErrorsUnrelated('62B00C5E-4187-4336-B569-44E0030DC729')),
            new DateTimeImmutable('2022-01-06 17:49:12')
        );

        $document = $capsule->exportToDocument();

        $signer = new DOMSigner();
        $signer->signDocument($document, $credentials);

        $this->domSigner = $signer;
    }

    public function testCreatedValues(): void
    {
        // signature text for preset capsule *must* be the following, see not used xmlns declarations
        /** @noinspection XmlUnusedNamespaceDeclaration */
        $expectedDigestSource = '<Cancelacion xmlns="http://cancelacfd.sat.gob.mx"'
            . ' xmlns:xsd="http://www.w3.org/2001/XMLSchema"'
            . ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'
            . ' Fecha="2022-01-06T17:49:12" RfcEmisor="EKU9003173C9">'
            . '<Folios>'
            . '<Folio Motivo="02" UUID="62B00C5E-4187-4336-B569-44E0030DC729"></Folio>'
            . '</Folios>'
            . '</Cancelacion>';

        $expectedDigestValue = '28wlg0suJ57t4P4P+LshbcH5PYE=';

        // signed info text for preset capsule *must* be the following, see not used xmlns declarations and C14N
        /** @noinspection XmlUnusedNamespaceDeclaration */
        $expectedSignedInfo = '<SignedInfo xmlns="http://www.w3.org/2000/09/xmldsig#"'
            . ' xmlns:xsd="http://www.w3.org/2001/XMLSchema"'
            . ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            . '<CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315">'
            . '</CanonicalizationMethod>'
            . '<SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1"></SignatureMethod>'
            . '<Reference URI="">'
            . '<Transforms>'
            . '<Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"></Transform>'
            . '</Transforms>'
            . '<DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1"></DigestMethod>'
            . '<DigestValue>' . $expectedDigestValue . '</DigestValue>'
            . '</Reference>'
            . '</SignedInfo>';

        $expectedSignedValue = implode('', [
            'h1USGSs4ziIxiWowgp4KItvI/4HlneSyAP+4wSK59NK2ym4Qlyxmtj9O7YCb6Sr6iNhPWBX4pYcN',
            'EOpS2rSmpADdhUlxHxxed5XUdfxGViBtSF8y8hahuTiImdE+d41BiXdu2ml/GUbXhDjbWImDYgAe',
            'vg5tQILJ02PuMNZYWE2WNUGbvwiT9239+vmKmxYtrZYTBaNfI5ESd3Bf6mOeu8qEGCfV0V8O8iBz',
            'rkxCuKbfkowe6a/CROcybPJ/WiwXNHP2pPS7FCvQScdObtC89UOCvafgIDEziWREU4SFMydApyhG',
            '9Hk/hB9MbsrDdAS/3OjZTNjfRCPV5bo2zAngHg==',
        ]);

        $this->assertSame($expectedDigestSource, $this->domSigner->getDigestSource());
        $this->assertSame($expectedDigestValue, $this->domSigner->getDigestValue());
        $this->assertSame($expectedSignedInfo, $this->domSigner->getSignedInfoSource());
        $this->assertSame($expectedSignedValue, $this->domSigner->getSignedInfoValue());
    }
}
