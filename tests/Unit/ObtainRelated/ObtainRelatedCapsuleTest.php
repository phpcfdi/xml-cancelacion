<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit\ObtainRelated;

use PhpCfdi\XmlCancelacion\Definitions\RfcRole;
use PhpCfdi\XmlCancelacion\ObtainRelated\ObtainRelatedCapsule;
use PhpCfdi\XmlCancelacion\Tests\TestCase;

class ObtainRelatedCapsuleTest extends TestCase
{
    public function testConstructAndExportToDocument(): void
    {
        $uuid = '11111111-2222-3333-4444-000000000001';
        $rfc = 'LAN7008173R5';
        $role = RfcRole::receiver();
        $pacRfc = 'CVD110412TF6';

        $capsule = new ObtainRelatedCapsule($uuid, $rfc, $role, $pacRfc);

        $this->assertSame($uuid, $capsule->uuid());
        $this->assertSame($rfc, $capsule->rfc());
        $this->assertSame($role, $capsule->role());
        $this->assertSame($pacRfc, $capsule->pacRfc());

        $this->assertTrue($capsule->belongsToRfc($rfc));
        $this->assertFalse($capsule->belongsToRfc($pacRfc));

        $expectedFile = $this->filePath('obtain-related-document.xml');
        $this->assertXmlStringEqualsXmlFile($expectedFile, $capsule->exportToDocument());
    }
}
