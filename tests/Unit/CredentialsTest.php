<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit;

use PhpCfdi\Credentials\Credential as PhpCfdiCredential;
use PhpCfdi\XmlCancelacion\Credentials;
use PhpCfdi\XmlCancelacion\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;

class CredentialsTest extends TestCase
{
    public function testCreateCsdWithInvalidPassword(): void
    {
        $cerContent = $this->filePath('LAN7008173R5.cer.pem');
        $keyContent = $this->filePath('LAN7008173R5.key.pem');
        $passPhrase = 'this is sparta!';

        $credential = new Credentials($cerContent, $keyContent, $passPhrase);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot load certificate and private key');
        $credential->certificateIssuerName(); // something that require csd creation
    }

    public function testCreateCsdWithInvalidCredential(): void
    {
        $cerContent = $this->filePath('LAN7008173R5.cer.pem');
        $keyContent = $this->filePath('LAN7008173R5.key.pem');
        $passPhrase = trim($this->fileContents('LAN7008173R5.password'));

        $phpCfdiCredential = $this->getMockBuilder(PhpCfdiCredential::class)
            ->disableOriginalConstructor()
            ->getMock();
        $phpCfdiCredential->method('isCsd')->willReturn(false);

        /** @var Credentials&MockObject $credential */
        $credential = $this->getMockBuilder(Credentials::class)
            ->setConstructorArgs([$cerContent, $keyContent, $passPhrase])
            ->onlyMethods(['makePhpCfdiCredential'])
            ->getMock();
        $credential->expects($this->once())->method('makePhpCfdiCredential')->willReturn($phpCfdiCredential);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot load certificate and private key');
        $credential->certificateIssuerName(); // something that require csd creation
    }
}
