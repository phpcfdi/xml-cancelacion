<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit\Exceptions;

use PhpCfdi\XmlCancelacion\Contracts\CapsuleInterface;
use PhpCfdi\XmlCancelacion\Exceptions\InvalidCapsuleType;
use PhpCfdi\XmlCancelacion\Exceptions\XmlCancelacionLogicException;
use PhpCfdi\XmlCancelacion\Tests\TestCase;
use PhpCfdi\XmlCancelacion\Tests\Unit\Contracts\FakeCapsule;
use PHPUnit\Framework\MockObject\MockObject;

class InvalidCapsuleTypeTest extends TestCase
{
    public function testCreateAndValues(): void
    {
        /** @var FakeCapsule&MockObject $capsule */
        $capsule = $this->createMock(CapsuleInterface::class);
        $expected = FakeCapsule::class;
        $exception = new InvalidCapsuleType($capsule, $expected);
        $this->assertInstanceOf(XmlCancelacionLogicException::class, $exception);
        $this->assertSame($capsule, $exception->getCapsule());
        $this->assertSame($expected, $exception->getExpected());
        $this->assertStringMatchesFormat(
            'Given capsule %s is not expected type ' . $expected,
            $exception->getMessage()
        );
    }
}
