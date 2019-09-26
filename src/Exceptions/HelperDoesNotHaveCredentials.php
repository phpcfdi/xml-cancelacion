<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Exceptions;

class HelperDoesNotHaveCredentials extends XmlCancelacionLogicException
{
    public function __construct()
    {
        parent::__construct('The helper object has no credentials set');
    }
}
