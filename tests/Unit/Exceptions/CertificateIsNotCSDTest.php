<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit\Exceptions;

use PhpCfdi\XmlCancelacion\Exceptions\CertificateIsNotCSD;
use PhpCfdi\XmlCancelacion\Exceptions\XmlCancelacionRuntimeException;
use PhpCfdi\XmlCancelacion\Tests\TestCase;

final class CertificateIsNotCSDTest extends TestCase
{
    public function testCreateAndValues(): void
    {
        $exception = new CertificateIsNotCSD('12345678');
        $this->assertInstanceOf(XmlCancelacionRuntimeException::class, $exception);
        $this->assertSame('12345678', $exception->getSerialNumber());
        $this->assertSame('The certificate [12345678] is not a CSD', $exception->getMessage());
    }
}
