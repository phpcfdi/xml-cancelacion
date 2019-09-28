<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\System\SignerImplementations;

use PhpCfdi\XmlCancelacion\Signers\DOMSigner;
use PhpCfdi\XmlCancelacion\Signers\SignerInterface;

class ImplementationDOMSignerTest extends SignerImplementationTestCase
{
    public function createSigner(): SignerInterface
    {
        return new DOMSigner();
    }
}
