<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit\Signers;

use PhpCfdi\XmlCancelacion\Signers\CreateKeyInfoElementTrait;

class CreateKeyInfoElementTraitImplementor
{
    use CreateKeyInfoElementTrait {
        createKeyInfoElement as public;
    }
}
