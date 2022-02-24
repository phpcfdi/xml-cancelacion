<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\System\SignerImplementations;

use PhpCfdi\XmlCancelacion\Signers\SignerInterface;
use PhpCfdi\XmlCancelacion\Signers\XmlSecLibsSigner;

final class ImplementationXmlSecLibsSignerTest extends SignerImplementationTestCase
{
    public function createSigner(): SignerInterface
    {
        return new XmlSecLibsSigner();
    }
}
