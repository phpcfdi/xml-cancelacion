<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit\Capsules;

use DateTimeImmutable;
use PhpCfdi\XmlCancelacion\Capsules\Cancellation;
use PhpCfdi\XmlCancelacion\Tests\TestCase;

class CancellationTest extends TestCase
{
    public function testConstructAndGetParameters(): void
    {
        $date = new DateTimeImmutable('2019-01-13 14:15:16');
        $uuids = [
            '12345678-1234-1234-1234-123456789001',
            '12345678-1234-1234-1234-123456789002',
        ];
        $rfc = 'LAN7008173R5';
        $capsule = new Cancellation($rfc, $uuids, $date);
        $this->assertSame($rfc, $capsule->rfc());
        $this->assertSame($uuids, $capsule->uuids());
        $this->assertSame($date, $capsule->date());

        $this->assertTrue($capsule->belongsToRfc($rfc));
        $this->assertFalse($capsule->belongsToRfc('AAA010101AAA'));
    }

    public function testCount(): void
    {
        $date = new DateTimeImmutable('2019-01-13 14:15:16');
        $uuids = [
            '12345678-1234-1234-1234-123456789001',
            '12345678-1234-1234-1234-123456789002',
        ];
        $rfc = 'LAN7008173R5';
        $capsule = new Cancellation($rfc, $uuids, $date);
        $this->assertCount(2, $capsule);
    }

    public function testExportToDocument(): void
    {
        // even when UUID using ampersand is not correct, it does not have to break our library
        $uuids = ['11111111-2222-3333-4444-000000000001', '11111111-2222-3333-4444-000000000002'];
        $dateTime = new DateTimeImmutable('2019-01-13 14:15:16');
        $capsule = new Cancellation('LAN7008173R5', $uuids, $dateTime);
        $expectedFile = $this->filePath('cancellation-document.xml');
        $this->assertXmlStringEqualsXmlFile($expectedFile, $capsule->exportToDocument());
    }

    public function testCreateDocumentWithAmpersandsOnUuids(): void
    {
        // even when UUID using ampersand is not correct, it does not have to break our library
        $badUuidWithAmpersand = 'E174F807-&&&&-4CF6-9B11-2A013B12F398';
        $dateTime = new DateTimeImmutable('2019-04-05T16:29:17');
        $capsule = new Cancellation('LAN7008173R5', [$badUuidWithAmpersand], $dateTime);
        $document = $capsule->exportToDocument();
        $this->assertStringContainsString(htmlspecialchars($badUuidWithAmpersand, ENT_XML1), $document->saveXML());
    }
}
