<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit\Capsules;

use DateTimeImmutable;
use PhpCfdi\XmlCancelacion\Capsules\CancellationAnswer;
use PhpCfdi\XmlCancelacion\Definitions\CancelAnswer;
use PhpCfdi\XmlCancelacion\Tests\TestCase;

class CancellationAnswerTest extends TestCase
{
    public function testConstructAndExportToDocument(): void
    {
        $uuid = '11111111-2222-3333-4444-000000000001';
        $rfc = 'LAN7008173R5';
        $answer = CancelAnswer::accept();
        $pacRfc = 'CVD110412TF6';
        $date = new DateTimeImmutable('2019-01-13 14:15:16');

        $capsule = new CancellationAnswer($rfc, $uuid, $answer, $pacRfc, $date);

        $this->assertSame($uuid, $capsule->uuid());
        $this->assertSame($rfc, $capsule->rfc());
        $this->assertSame($answer, $capsule->answer());
        $this->assertSame($pacRfc, $capsule->pacRfc());
        $this->assertSame($date, $capsule->dateTime());

        $this->assertTrue($capsule->belongsToRfc($rfc));
        $this->assertFalse($capsule->belongsToRfc($pacRfc));

        $expectedFile = $this->filePath('cancellation-answer-document.xml');
        $this->assertXmlStringEqualsXmlFile($expectedFile, $capsule->exportToDocument());
    }
}
