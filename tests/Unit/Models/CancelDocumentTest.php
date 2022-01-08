<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit\Models;

use LogicException;
use PhpCfdi\XmlCancelacion\Models\CancelDocument;
use PhpCfdi\XmlCancelacion\Models\CancelReason;
use PhpCfdi\XmlCancelacion\Models\Uuid;
use PhpCfdi\XmlCancelacion\Tests\TestCase;

final class CancelDocumentTest extends TestCase
{
    public function testProperties(): void
    {
        $uuid = new Uuid('12345678-2222-3333-4444-123456789012');
        $substituteOf = new Uuid('12345678-2222-3333-4444-123456789013');
        $reason = CancelReason::withErrorsUnrelated();
        $document = new CancelDocument($uuid, $reason, $substituteOf);

        $this->assertSame($uuid, $document->uuid());
        $this->assertSame($reason, $document->reason());
        $this->assertSame($substituteOf, $document->substituteOf());
    }

    public function testNewWithErrorsRelated(): void
    {
        $uuid = '12345678-2222-3333-4444-123456789012';
        $substituteOf = '12345678-2222-3333-4444-123456789013';
        $document = CancelDocument::newWithErrorsRelated($uuid, $substituteOf);
        $this->assertSame($uuid, $document->uuid()->getValue());
        $this->assertTrue($document->reason()->isWithErrorsRelated());
        $this->assertTrue($document->hasSubstituteOf());
        $this->assertSame($substituteOf, $document->substituteOf()->getValue());
    }

    public function testNewWithErrorsUnrelated(): void
    {
        $uuid = '12345678-2222-3333-4444-123456789012';
        $document = CancelDocument::newWithErrorsUnrelated($uuid);
        $this->assertSame($uuid, $document->uuid()->getValue());
        $this->assertTrue($document->reason()->isWithErrorsUnrelated());
        $this->assertFalse($document->hasSubstituteOf());
    }

    public function testNewNotExecuted(): void
    {
        $uuid = '12345678-2222-3333-4444-123456789012';
        $document = CancelDocument::newNotExecuted($uuid);
        $this->assertSame($uuid, $document->uuid()->getValue());
        $this->assertTrue($document->reason()->isNotExecuted());
        $this->assertFalse($document->hasSubstituteOf());
    }

    public function testNewNormativeToGlobal(): void
    {
        $uuid = '12345678-2222-3333-4444-123456789012';
        $document = CancelDocument::newNormativeToGlobal($uuid);
        $this->assertSame($uuid, $document->uuid()->getValue());
        $this->assertTrue($document->reason()->isNormativeToGlobal());
        $this->assertFalse($document->hasSubstituteOf());
    }

    public function testGetSubstituteOfPropertyWithNullValueThrowsException(): void
    {
        $document = CancelDocument::newWithErrorsUnrelated('12345678-2222-3333-4444-123456789012');
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The property substituteOf is not defined');
        $document->substituteOf();
    }
}
