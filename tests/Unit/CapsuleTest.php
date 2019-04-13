<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit;

use DateTimeImmutable;
use PhpCfdi\XmlCancelacion\Capsule;
use PhpCfdi\XmlCancelacion\Tests\TestCase;

class CapsuleTest extends TestCase
{
    public function testConstructAndGetParameters(): void
    {
        $date = new DateTimeImmutable('2019-01-13 14:15:16');
        $uuids = [
            '12345678-1234-1234-1234-123456789001',
            '12345678-1234-1234-1234-123456789002',
        ];
        $rfc = 'LAN7008173R5';
        $capsule = new Capsule($rfc, $uuids, $date);
        $this->assertSame($rfc, $capsule->rfc());
        $this->assertSame($uuids, $capsule->uuids());
        $this->assertSame($date, $capsule->date());
    }

    public function testConstructCapsuleWithoutDateGivesNow(): void
    {
        $uuids = [
            '12345678-1234-1234-1234-123456789001',
            '12345678-1234-1234-1234-123456789002',
        ];
        $rfc = 'LAN7008173R5';
        $date = new DateTimeImmutable('now');
        $capsule = new Capsule($rfc, $uuids, $date);
        $this->assertSame(0, $date->getTimestamp() - $capsule->date()->getTimestamp());
    }

    public function testCount(): void
    {
        $date = new DateTimeImmutable('2019-01-13 14:15:16');
        $uuids = [
            '12345678-1234-1234-1234-123456789001',
            '12345678-1234-1234-1234-123456789002',
        ];
        $rfc = 'LAN7008173R5';
        $capsule = new Capsule($rfc, $uuids, $date);
        $this->assertCount(2, $capsule);
    }
}
