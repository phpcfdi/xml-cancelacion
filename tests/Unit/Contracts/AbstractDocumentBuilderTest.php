<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit\Contracts;

use PhpCfdi\XmlCancelacion\Contracts\CapsuleInterface;
use PhpCfdi\XmlCancelacion\Exceptions\InvalidCapsuleType;
use PhpCfdi\XmlCancelacion\Tests\TestCase;

/** @covers \PhpCfdi\XmlCancelacion\Contracts\AbstractCapsuleDocumentBuilder */
class AbstractDocumentBuilderTest extends TestCase
{
    public function testDefaultNameSpacesExactContentAndOrder(): void
    {
        $this->assertSame([
            'xsd' => 'http://www.w3.org/2001/XMLSchema',
            'xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
        ], FakeDocumentBuilder::defaultExtraNamespaces());
    }

    public function testConstructWithExtraNamespaces(): void
    {
        $extraNamespaces = [
            'foo' => 'http://example.com/foo',
            'bar' => 'http://example.com/bar',
        ];
        $builder = new FakeDocumentBuilder($extraNamespaces);
        $this->assertSame($extraNamespaces, $builder->extraNamespaces());
    }

    public function testConstructWithEmptyNamespaces(): void
    {
        $extraNamespaces = [];
        $builder = new FakeDocumentBuilder($extraNamespaces);
        $this->assertSame($extraNamespaces, $builder->extraNamespaces());
    }

    public function testConstructWithDefaultNamespaces(): void
    {
        $builder = new FakeDocumentBuilder();
        $this->assertSame($builder->defaultExtraNamespaces(), $builder->extraNamespaces());
    }

    public function testMakeDocument(): void
    {
        $builder = new FakeDocumentBuilder();
        $document = $builder->makeDocument(new FakeCapsule('COSC8001137NA'));
        $expectedXml = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<fake xmlns="http://tempuri.org/fake"'
            . ' xmlns:xsd="http://www.w3.org/2001/XMLSchema"'
            . ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>';
        $this->assertXmlStringEqualsXmlString($expectedXml, $document);
    }

    public function testMakeDocumentWithInvalidCapsule(): void
    {
        $builder = new FakeDocumentBuilder();
        /** @var CapsuleInterface&FakeCapsule $capsule */
        $capsule = $this->createMock(CapsuleInterface::class);
        $this->expectException(InvalidCapsuleType::class);
        $builder->makeDocument($capsule);
    }
}
