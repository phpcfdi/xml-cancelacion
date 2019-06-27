<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit;

use DateTimeImmutable;
use PhpCfdi\XmlCancelacion\Capsule;
use PhpCfdi\XmlCancelacion\CapsuleSigner;
use PhpCfdi\XmlCancelacion\Tests\TestCase;

class CapsuleSignerTest extends TestCase
{
    public function testCreatedCapsuleSignerHasDefaultNameSpaces(): void
    {
        $signer = new CapsuleSigner();
        $this->assertSame($signer->defaultExtraNamespaces(), $signer->extraNamespaces());
    }

    public function testDefaultNameSpacesExactContentAndOrder(): void
    {
        $signer = new CapsuleSigner();
        $this->assertSame([
            'xsd' => 'http://www.w3.org/2001/XMLSchema',
            'xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
        ], $signer->defaultExtraNamespaces());
    }

    public function testCreateDocumentWithAmpersandsOnUuids(): void
    {
        // even when UUID using ampersand is not correct, it does not have to break our library
        $signer = new CapsuleSigner();
        $badUuidWithAmpersand = 'E174F807-&&&&-4CF6-9B11-2A013B12F398';
        $capsule = new Capsule('LAN7008173R5', [$badUuidWithAmpersand], new DateTimeImmutable('2019-04-05T16:29:17'));
        $document = $signer->createDocument($capsule);
        $this->assertStringContainsString(htmlspecialchars($badUuidWithAmpersand, ENT_XML1), $document->saveXML());
    }
}
