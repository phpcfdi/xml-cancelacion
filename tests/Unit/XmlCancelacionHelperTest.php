<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit;

use DateTimeImmutable;
use LogicException;
use PhpCfdi\XmlCancelacion\Capsule;
use PhpCfdi\XmlCancelacion\Credentials;
use PhpCfdi\XmlCancelacion\Tests\TestCase;
use PhpCfdi\XmlCancelacion\XmlCancelacionHelper;
use PHPUnit\Framework\MockObject\MockObject;

/** @covers \PhpCfdi\XmlCancelacion\XmlCancelacionHelper */
class XmlCancelacionHelperTest extends TestCase
{
    /** @return Credentials&MockObject */
    private function createFakeCredentials(): Credentials
    {
        /** @var Credentials&MockObject $credentials */
        $credentials = $this->createMock(Credentials::class);
        return $credentials;
    }

    private function createRealCredentials(): Credentials
    {
        $cerFile = $this->filePath('LAN7008173R5.cer.pem');
        $keyFile = $this->filePath('LAN7008173R5.key.pem');
        $passPhrase = trim($this->fileContents('LAN7008173R5.password'));
        return new Credentials($cerFile, $keyFile, $passPhrase);
    }

    public function testCredentialChanges(): void
    {
        $fakeCredentials = $this->createFakeCredentials();

        $helper = new XmlCancelacionHelper();
        $this->assertFalse($helper->hasCredentials());

        $this->assertSame($helper, $helper->setCredentials($fakeCredentials));
        $this->assertTrue($helper->hasCredentials());

        $cerFile = $this->filePath('LAN7008173R5.cer.pem');
        $keyFile = $this->filePath('LAN7008173R5.key.pem');
        $passPhrase = trim($this->fileContents('LAN7008173R5.password'));
        $this->assertSame($helper, $helper->setNewCredentials($cerFile, $keyFile, $passPhrase));
        $this->assertTrue($helper->hasCredentials());
        $this->assertSame('LAN7008173R5', $helper->getCredentials()->rfc());
    }

    public function testMakeCallsMakeUuids(): void
    {
        $uuid = '11111111-2222-3333-4444-000000000001';
        $predefinedReturn = 'signed-xml';

        /** @var XmlCancelacionHelper&MockObject $helper */
        $helper = $this->getMockBuilder(XmlCancelacionHelper::class)
            ->onlyMethods(['makeUuids'])
            ->getMock();
        $helper->expects($this->once())
            ->method('makeUuids')
            ->with($this->equalTo([$uuid]), $this->isNull())
            ->willReturn($predefinedReturn);

        $this->assertSame($predefinedReturn, $helper->make($uuid));
    }

    public function testMakeUuids(): void
    {
        $credentials = $this->createRealCredentials();
        $rfc = $credentials->rfc();
        $uuids = ['11111111-2222-3333-4444-000000000001', '11111111-2222-3333-4444-000000000002'];
        $helper = new class() extends XmlCancelacionHelper {
            /** @var Capsule */
            private $capsule;

            protected function createCapsule(string $rfc, array $uuids, DateTimeImmutable $dateTime): Capsule
            {
                $this->capsule = parent::createCapsule($rfc, $uuids, $dateTime);
                return $this->capsule;
            }

            public function getCreatedCapsule(): Capsule
            {
                return $this->capsule;
            }
        };

        $now = new DateTimeImmutable();
        $result = $helper->setCredentials($credentials)->makeUuids($uuids);
        $this->assertStringContainsString('<Signature xmlns="http://www.w3.org/2000/09/xmldsig#">', $result);

        /** @var Capsule $spyCapsule */
        $spyCapsule = $helper->getCreatedCapsule();
        $spyDate = $spyCapsule->date();
        $this->assertSame($rfc, $spyCapsule->rfc());
        $this->assertSame($uuids, $spyCapsule->uuids());
        $this->assertTrue($spyDate > $now->modify('-1 second') && $spyDate < $now->modify('+1 second'));
    }

    public function testGetCredentialsWithoutSettingBefore(): void
    {
        $helper = new XmlCancelacionHelper();
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The object has no credentials');
        $helper->getCredentials();
    }

    public function testCanConstructWithCredentials(): void
    {
        $credentials = $this->createFakeCredentials();
        $helper = new XmlCancelacionHelper($credentials);
        $this->assertSame($credentials, $helper->getCredentials());
    }
}
