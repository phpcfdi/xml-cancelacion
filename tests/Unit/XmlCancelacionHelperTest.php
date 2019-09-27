<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit;

use DateTimeImmutable;
use PhpCfdi\XmlCancelacion\Capsules\CancellationCapsule;
use PhpCfdi\XmlCancelacion\Capsules\CapsuleInterface;
use PhpCfdi\XmlCancelacion\Credentials;
use PhpCfdi\XmlCancelacion\Exceptions\HelperDoesNotHaveCredentials;
use PhpCfdi\XmlCancelacion\Signers\DOMSigner;
use PhpCfdi\XmlCancelacion\Signers\SignerInterface;
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

    public function testSignerChanges(): void
    {
        /** @var SignerInterface $fakeSigner */
        $fakeSigner = $this->createMock(SignerInterface::class);
        $helper = new XmlCancelacionHelper();
        $this->assertInstanceOf(
            DOMSigner::class,
            $helper->getSigner(),
            'Default signer must be a DOMSigner instance'
        );
        $this->assertSame($helper, $helper->setSigner($fakeSigner));
        $this->assertSame($fakeSigner, $helper->getSigner());
    }

    public function testMakeCallsSignCancellation(): void
    {
        $dateTime = new DateTimeImmutable();
        $uuid = '11111111-2222-3333-4444-000000000001';

        $credentials = $this->createRealCredentials();
        $expectedCapsule = new CancellationCapsule('LAN7008173R5', [$uuid], $dateTime);

        $predefinedReturn = 'signed-xml';

        /** @var XmlCancelacionHelper&MockObject $helper */
        $helper = $this->getMockBuilder(XmlCancelacionHelper::class)
            ->onlyMethods(['signCapsule'])
            ->getMock();
        $helper->expects($this->once())
            ->method('signCapsule')
            ->with($this->equalTo($expectedCapsule))
            ->willReturn($predefinedReturn);
        $helper->setCredentials($credentials);

        $this->assertSame($predefinedReturn, $helper->signCancellation($uuid, $dateTime));
    }

    public function testMakeUuids(): void
    {
        $credentials = $this->createRealCredentials();
        $rfc = $credentials->rfc();
        $uuids = ['11111111-2222-3333-4444-000000000001', '11111111-2222-3333-4444-000000000002'];
        $helper = new class() extends XmlCancelacionHelper {
            /** @var CapsuleInterface */
            private $capsule;

            public function signCapsule(CapsuleInterface $capsule): string
            {
                $this->capsule = $capsule;
                return parent::signCapsule($capsule);
            }

            public function getCreatedCapsule(): CapsuleInterface
            {
                return $this->capsule;
            }
        };

        $now = new DateTimeImmutable();
        $result = $helper->setCredentials($credentials)->signCancellationUuids($uuids);
        $this->assertStringContainsString('<Signature xmlns="http://www.w3.org/2000/09/xmldsig#">', $result);

        /** @var CancellationCapsule $spyCapsule */
        $spyCapsule = $helper->getCreatedCapsule();
        $this->assertInstanceOf(CancellationCapsule::class, $spyCapsule);
        $spyDate = $spyCapsule->date();
        $this->assertSame($rfc, $spyCapsule->rfc());
        $this->assertSame($uuids, $spyCapsule->uuids());
        $this->assertTrue($spyDate > $now->modify('-1 second') && $spyDate < $now->modify('+1 second'));
    }

    public function testGetCredentialsWithoutSettingBefore(): void
    {
        $helper = new XmlCancelacionHelper();
        $this->expectException(HelperDoesNotHaveCredentials::class);
        $helper->getCredentials();
    }

    public function testCanConstructWithCredentials(): void
    {
        $credentials = $this->createFakeCredentials();
        $helper = new XmlCancelacionHelper($credentials);
        $this->assertSame($credentials, $helper->getCredentials());
    }
}
