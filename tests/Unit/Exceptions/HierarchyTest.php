<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit\Exceptions;

use LogicException;
use PhpCfdi\XmlCancelacion\Exceptions\XmlCancelacionException;
use PhpCfdi\XmlCancelacion\Exceptions\XmlCancelacionLogicException;
use PhpCfdi\XmlCancelacion\Exceptions\XmlCancelacionRuntimeException;
use PhpCfdi\XmlCancelacion\Tests\TestCase;
use RuntimeException;

final class HierarchyTest extends TestCase
{
    public function testXmlCancelationRuntimeExceptionImplementsXmlCancelationException(): void
    {
        $specimen = $this->createMock(XmlCancelacionRuntimeException::class);
        $this->assertInstanceOf(XmlCancelacionException::class, $specimen);
        $this->assertInstanceOf(RuntimeException::class, $specimen);
    }

    public function testXmlCancelationLogicExceptionImplementsXmlCancelationException(): void
    {
        $specimen = $this->createMock(XmlCancelacionLogicException::class);
        $this->assertInstanceOf(XmlCancelacionException::class, $specimen);
        $this->assertInstanceOf(LogicException::class, $specimen);
    }
}
