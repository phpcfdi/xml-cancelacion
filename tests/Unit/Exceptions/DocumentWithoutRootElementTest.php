<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit\Exceptions;

use PhpCfdi\XmlCancelacion\Exceptions\DocumentWithoutRootElement;
use PhpCfdi\XmlCancelacion\Exceptions\XmlCancelacionLogicException;
use PhpCfdi\XmlCancelacion\Tests\TestCase;

class DocumentWithoutRootElementTest extends TestCase
{
    public function testCreateAndValues(): void
    {
        $exception = new DocumentWithoutRootElement();
        $this->assertInstanceOf(XmlCancelacionLogicException::class, $exception);
        $this->assertSame('DOM Document does not have a root element', $exception->getMessage());
    }
}
