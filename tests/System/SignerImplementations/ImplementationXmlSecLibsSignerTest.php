<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\System\SignerImplementations;

use PhpCfdi\XmlCancelacion\Contracts\SignerInterface;
use PhpCfdi\XmlCancelacion\XmlSecLibsSigner;

class ImplementationXmlSecLibsSignerTest extends SignerImplementationTestCase
{
    public function createSigner(): SignerInterface
    {
        return new XmlSecLibsSigner();
    }
}
