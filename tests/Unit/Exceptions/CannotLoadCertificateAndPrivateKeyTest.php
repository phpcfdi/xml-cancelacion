<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit\Exceptions;

use PhpCfdi\XmlCancelacion\Exceptions\CannotLoadCertificateAndPrivateKey;
use PhpCfdi\XmlCancelacion\Exceptions\XmlCancelacionRuntimeException;
use PhpCfdi\XmlCancelacion\Tests\TestCase;
use RuntimeException;

final class CannotLoadCertificateAndPrivateKeyTest extends TestCase
{
    public function testCreateAndValues(): void
    {
        $previous = new RuntimeException('dummy');
        $exception = new CannotLoadCertificateAndPrivateKey('cer', 'key', 'pass', $previous);
        $this->assertInstanceOf(XmlCancelacionRuntimeException::class, $exception);
        $this->assertSame('cer', $exception->getCertificateFile());
        $this->assertSame('key', $exception->getPrivateKeyFile());
        $this->assertSame('pass', $exception->getPassPhrase());
        $this->assertSame($previous, $exception->getPrevious());
        $this->assertSame('Cannot load certificate and private key', $exception->getMessage());
    }
}
