<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\System;

use DateTimeImmutable;
use DOMDocument;
use PhpCfdi\XmlCancelacion\Capsule;
use PhpCfdi\XmlCancelacion\CapsuleSigner;
use PhpCfdi\XmlCancelacion\Credentials;
use PhpCfdi\XmlCancelacion\DOMSigner;
use PhpCfdi\XmlCancelacion\Tests\TestCase;
use RobRichards\XMLSecLibs\XMLSecEnc;
use RobRichards\XMLSecLibs\XMLSecurityDSig;

class XmlSignedTest extends TestCase
{
    /** @var DOMSigner */
    private $domSigner;

    /** @var string */
    private $signature;

    /** @var Capsule */
    private $capsule;

    /** @var Credentials */
    private $signObjects;

    public function setUp(): void
    {
        parent::setUp();

        $cerContent = $this->filePath('LAN7008173R5.cer.pem');
        $keyContent = $this->filePath('LAN7008173R5.key.pem');
        $passPhrase = trim($this->fileContentPath('LAN7008173R5.password'));
        $signObjects = new Credentials($cerContent, $keyContent, $passPhrase);

        $capsule = new Capsule(
            'LAN7008173R5',
            ['E174F807-BEFA-4CF6-9B11-2A013B12F398'],
            new DateTimeImmutable('2019-04-05T16:29:17')
        );

        $transpiler = new CapsuleSigner();
        $document = $transpiler->createDocument($capsule);

        $signer = new DOMSigner($document);
        $signer->sign($signObjects);

        $this->capsule = $capsule;
        $this->signObjects = $signObjects;
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
            . '<CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315"></CanonicalizationMethod>'
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

    public function testSignatureIsValidUsingXmlSecLib(): void
    {
        $document = new DOMDocument();
        $document->loadXML($this->signature);

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

    public function testCapsuleSigner(): void
    {
        $signer = new CapsuleSigner();
        $signature = $signer->sign($this->capsule, $this->signObjects);

        $this->assertSame($this->signature, $signature);
    }

    public function testWithPredefinedContent(): void
    {
        // file_put_contents($this->filePath('expected-signature.xml'), $this->signature);
        $this->assertXmlStringEqualsXmlFile($this->filePath('expected-signature.xml'), $this->signature);
    }
}
