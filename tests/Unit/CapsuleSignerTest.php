<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit;

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
}
