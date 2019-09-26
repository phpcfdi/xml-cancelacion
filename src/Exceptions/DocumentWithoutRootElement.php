<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Exceptions;

class DocumentWithoutRootElement extends XmlCancelacionLogicException
{
    public function __construct()
    {
        parent::__construct('DOM Document does not have a root element');
    }
}
