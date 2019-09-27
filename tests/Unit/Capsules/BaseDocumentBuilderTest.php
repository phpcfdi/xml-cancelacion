<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit\Capsules;

use PhpCfdi\XmlCancelacion\Capsules\BaseDocumentBuilder;
use PhpCfdi\XmlCancelacion\Tests\TestCase;

/** @covers \PhpCfdi\XmlCancelacion\Capsules\BaseDocumentBuilder */
class BaseDocumentBuilderTest extends TestCase
{
    public function testDefaultNameSpacesExactContentAndOrder(): void
    {
        $this->assertSame([
            'xsd' => 'http://www.w3.org/2001/XMLSchema',
            'xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
        ], BaseDocumentBuilder::defaultExtraNamespaces());
    }

    public function testConstructWithExtraNamespaces(): void
    {
        $extraNamespaces = [
            'foo' => 'http://example.com/foo',
            'bar' => 'http://example.com/bar',
        ];
        $builder = new BaseDocumentBuilder($extraNamespaces);
        $this->assertSame($extraNamespaces, $builder->extraNamespaces());
    }

    public function testConstructWithEmptyNamespaces(): void
    {
        $extraNamespaces = [];
        $builder = new BaseDocumentBuilder($extraNamespaces);
        $this->assertSame($extraNamespaces, $builder->extraNamespaces());
    }

    public function testConstructWithDefaultNamespaces(): void
    {
        $builder = new BaseDocumentBuilder();
        $this->assertSame($builder->defaultExtraNamespaces(), $builder->extraNamespaces());
    }

    public function testMakeDocument(): void
    {
        $builder = new BaseDocumentBuilder();
        $document = $builder->createBaseDocument('fake', 'http://tempuri.org/fake');
        $expectedXml = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<fake xmlns="http://tempuri.org/fake"'
            . ' xmlns:xsd="http://www.w3.org/2001/XMLSchema"'
            . ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>';
        $this->assertXmlStringEqualsXmlString($expectedXml, $document);
    }
}
