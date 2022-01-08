<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit\Models;

use PhpCfdi\XmlCancelacion\Models\CancelDocument;
use PhpCfdi\XmlCancelacion\Models\CancelDocuments;
use PhpCfdi\XmlCancelacion\Tests\TestCase;

final class CancelDocumentsTest extends TestCase
{
    public function testCreateEmpty(): void
    {
        $documents = new CancelDocuments();
        $this->assertCount(0, $documents);
    }

    public function testCreateWithMultipleContents(): void
    {
        $documentsArray = [
            CancelDocument::newWithErrorsRelated(
                '12345678-2222-3333-4444-1234567890AA',
                '12345678-2222-3333-4444-1234567890BB'
            ),
            CancelDocument::newWithErrorsUnrelated('12345678-2222-3333-4444-1234567890CC'),
            CancelDocument::newNotExecuted('12345678-2222-3333-4444-1234567890DD'),
            CancelDocument::newNormativeToGlobal('12345678-2222-3333-4444-1234567890EE'),
        ];
        $documents = new CancelDocuments(...$documentsArray);
        $this->assertCount(4, $documents);
        $this->assertSame($documentsArray, iterator_to_array($documents));
        $this->assertSame([
            '12345678-2222-3333-4444-1234567890AA',
            '12345678-2222-3333-4444-1234567890CC',
            '12345678-2222-3333-4444-1234567890DD',
            '12345678-2222-3333-4444-1234567890EE',
        ], $documents->uuids());
    }
}
