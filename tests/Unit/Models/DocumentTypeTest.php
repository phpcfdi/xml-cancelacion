<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit\Models;

use PhpCfdi\XmlCancelacion\Models\DocumentType;
use PhpCfdi\XmlCancelacion\Tests\TestCase;

final class DocumentTypeTest extends TestCase
{
    /** @return array<string, array{DocumentType, string}> */
    public function providerXmlNamespaceCancellation(): array
    {
        return [
            'cfdi' => [DocumentType::cfdi(), 'http://cancelacfd.sat.gob.mx'],
            'retention' => [DocumentType::retention(), 'http://www.sat.gob.mx/esquemas/retencionpago/1'],
        ];
    }

    /** @dataProvider providerXmlNamespaceCancellation */
    public function testXmlNamespaceCancellation(DocumentType $documentType, string $expectedXmlNamespace): void
    {
        $this->assertSame($expectedXmlNamespace, $documentType->xmlNamespaceCancellation());
    }
}
