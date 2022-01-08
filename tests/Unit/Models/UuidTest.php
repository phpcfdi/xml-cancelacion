<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit\Models;

use InvalidArgumentException;
use PhpCfdi\XmlCancelacion\Models\Uuid;
use PhpCfdi\XmlCancelacion\Tests\TestCase;

final class UuidTest extends TestCase
{
    /** @return array<string, array{string}> */
    public function providerValidateInvalidCases(): array
    {
        return [
            'incorrect length +1 start' => ['A12345678-2222-3333-4444-123456789012'],
            'incorrect length -1 start' => ['2345678-2222-3333-4444-12345678901'],
            'incorrect length +1 end' => ['12345678-2222-3333-4444-123456789012A'],
            'incorrect length -1 end' => ['12345678-2222-3333-4444-12345678901'],
            'incorrect pattern separator' => ['12345678.2222-3333-4444-123456789012'],
            'incorrect pattern letter' => ['12345678.2222-3333-4444-12345678901X'],
        ];
    }

    /** @dataProvider providerValidateInvalidCases */
    public function testValidateInvalidCases(string $value): void
    {
        $this->assertFalse(Uuid::isValid($value));
        $this->expectException(InvalidArgumentException::class);
        new Uuid($value);
    }

    /** @return array<string, array{string}> */
    public function providerValidateValidCases(): array
    {
        return [
            'numbers' => ['12345678-2222-3333-4444-123456789012'],
            'letters lower case' => ['aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeff'],
            'letters upper case' => ['AAAAAAAA-BBBB-CCCC-DDDD-EEEEEEEEEEFF'],
        ];
    }

    /** @dataProvider providerValidateValidCases */
    public function testValidateValidCases(string $value): void
    {
        $this->assertTrue(Uuid::isValid($value));
        $uuid = new Uuid($value);
        $this->assertSame(strtoupper($value), $uuid->getValue());
    }
}
