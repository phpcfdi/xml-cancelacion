<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit\Exceptions;

use PhpCfdi\XmlCancelacion\Exceptions\HelperDoesNotHaveCredentials;
use PhpCfdi\XmlCancelacion\Exceptions\XmlCancelacionLogicException;
use PhpCfdi\XmlCancelacion\Tests\TestCase;

final class HelperDoesNotHaveCredentialsTest extends TestCase
{
    public function testCreateAndValues(): void
    {
        $exception = new HelperDoesNotHaveCredentials();
        $this->assertInstanceOf(XmlCancelacionLogicException::class, $exception);
        $this->assertSame('The helper object has no credentials set', $exception->getMessage());
    }
}
