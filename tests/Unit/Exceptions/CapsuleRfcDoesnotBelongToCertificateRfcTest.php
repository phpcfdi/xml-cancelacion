<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit\Exceptions;

use PhpCfdi\XmlCancelacion\Exceptions\CapsuleRfcDoesnotBelongToCertificateRfc;
use PhpCfdi\XmlCancelacion\Exceptions\XmlCancelacionRuntimeException;
use PhpCfdi\XmlCancelacion\Tests\TestCase;
use PhpCfdi\XmlCancelacion\Tests\Unit\Capsules\FakeCapsule;

class CapsuleRfcDoesnotBelongToCertificateRfcTest extends TestCase
{
    public function testCreateAndValues(): void
    {
        $capsule = new FakeCapsule('COSC8001137NA');
        $rfc = 'AAA010101AAA';
        $exception = new CapsuleRfcDoesnotBelongToCertificateRfc($capsule, $rfc);
        $this->assertInstanceOf(XmlCancelacionRuntimeException::class, $exception);
        $this->assertSame($capsule, $exception->getCapsule());
        $this->assertSame($rfc, $exception->getCertificateRfc());
        $this->assertFalse($capsule->belongsToRfc($rfc));
        $this->assertSame('The capsule RFC does not belong to certificate RFC', $exception->getMessage());
    }
}
