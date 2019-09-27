<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\System;

use DateTimeImmutable;
use PhpCfdi\XmlCancelacion\Capsules\Cancellation;
use PhpCfdi\XmlCancelacion\Credentials;
use PhpCfdi\XmlCancelacion\Signers\DOMSigner;
use PhpCfdi\XmlCancelacion\Tests\TestCase;

class XmlSignedUsingDOMSignerTest extends TestCase
{
    /** @var \PhpCfdi\XmlCancelacion\Signers\DOMSigner */
    private $domSigner;

    /** @var string */
    private $signature;

    /** @var \PhpCfdi\XmlCancelacion\Capsules\Cancellation */
    private $capsule;

    /** @var Credentials */
    private $signObjects;

    public function setUp(): void
    {
        parent::setUp();

        $credentials = new Credentials(
            $this->filePath('LAN7008173R5.cer.pem'),
            $this->filePath('LAN7008173R5.key.pem'),
            trim($this->fileContents('LAN7008173R5.password'))
        );

        $capsule = new Cancellation(
            'LAN7008173R5',
            ['E174F807-BEFA-4CF6-9B11-2A013B12F398'],
            new DateTimeImmutable('2019-04-05T16:29:17')
        );

        $document = $capsule->exportToDocument();

        $signer = new DOMSigner();
        $signer->signDocument($document, $credentials);

        $this->capsule = $capsule;
        $this->signObjects = $credentials;
        $this->domSigner = $signer;
        $this->signature = $document->saveXML();
    }

    public function testCreatedValues(): void
    {
        // signature text for preset capsule *must* be the following, see not used xmlns declarations
        /** @noinspection XmlUnusedNamespaceDeclaration */
        $expectedDigestSource = '<Cancelacion xmlns="http://cancelacfd.sat.gob.mx"'
            . ' xmlns:xsd="http://www.w3.org/2001/XMLSchema"'
            . ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'
            . ' Fecha="2019-04-05T16:29:17" RfcEmisor="LAN7008173R5">'
            . '<Folios><UUID>E174F807-BEFA-4CF6-9B11-2A013B12F398</UUID></Folios>'
            . '</Cancelacion>';

        $expectedDigestValue = 'j2x4spEq57R1mQD9lwXh2mmOyK8=';

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
            . '<DigestValue>j2x4spEq57R1mQD9lwXh2mmOyK8=</DigestValue>'
            . '</Reference>'
            . '</SignedInfo>';

        $expectedSignedValue = 'e0Cyi/rXOTFwW8ckNnwQEQ1oC6m73PDvExunnniCsZWQrDRV2SiaH9NoAhJhb5W9p5vJgB+PWu4J6uchG7Ei'
            . 'kDPbDPw19K3B7uZKTH7tZLffV/bZx6rozzreInvP+S1HhrnOqLPwebBm3Q3yRQk3pbaW2sHFPPuRPLqP+1h3Fegv4GEnwy+0G7LRg'
            . '3H05v6fDXvONgikCrC2sdzA0kM6qvrOpGfbgBd4au7eFFRjCA4oX9zcQUG9E4m+uVovj0ebp4EqDn9SC+Az3fi5AHom6adju8wx4u'
            . 'Jvi8isVg8ZP9KcuqEfXhIkyFutJrD61l00+XyZe4n5T1Aya+Ta0Q6NrA==';

        $this->assertSame($expectedDigestSource, $this->domSigner->getDigestSource());
        $this->assertSame($expectedDigestValue, $this->domSigner->getDigestValue());
        $this->assertSame($expectedSignedInfo, $this->domSigner->getSignedInfoSource());
        $this->assertSame($expectedSignedValue, $this->domSigner->getSignedInfoValue());
    }
}
