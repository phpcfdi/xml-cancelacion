<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit\Models;

use PhpCfdi\XmlCancelacion\Models\CancelReason;
use PhpCfdi\XmlCancelacion\Tests\TestCase;

final class CancelReasonTest extends TestCase
{
    /** @return array<string, array{CancelReason, int, string}> */
    public function providerEntries(): array
    {
        return [
            'with errors related' => [CancelReason::withErrorsRelated(), 1, '01'],
            'with errors unrelated' => [CancelReason::withErrorsUnrelated(), 2, '02'],
            'not executed' => [CancelReason::notExecuted(), 3, '03'],
            'normative to global' => [CancelReason::normativeToGlobal(), 4, '04'],
        ];
    }

    /** @dataProvider providerEntries */
    public function testEntries(CancelReason $entry, int $expectedIndex, string $expectedValue): void
    {
        $this->assertSame($expectedIndex, $entry->index());
        $this->assertSame($expectedValue, $entry->value());
    }

    /** @dataProvider providerEntries */
    public function testCreatedByIndex(CancelReason $entry, int $index, string $value): void
    {
        $created = new CancelReason($index);
        $this->assertEquals($entry, $created);
    }

    /** @dataProvider providerEntries */
    public function testCreatedByValue(CancelReason $entry, int $index, string $value): void
    {
        $created = new CancelReason($value);
        $this->assertEquals($entry, $created);
    }
}
