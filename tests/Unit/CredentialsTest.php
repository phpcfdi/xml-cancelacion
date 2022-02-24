<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit;

use PhpCfdi\Credentials\Credential;
use PhpCfdi\Credentials\Credential as PhpCfdiCredential;
use PhpCfdi\XmlCancelacion\Credentials;
use PhpCfdi\XmlCancelacion\Exceptions\CannotLoadCertificateAndPrivateKey;
use PhpCfdi\XmlCancelacion\Exceptions\CertificateIsNotCSD;
use PhpCfdi\XmlCancelacion\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/** @covers \PhpCfdi\XmlCancelacion\Credentials */
final class CredentialsTest extends TestCase
{
    public function testValidCredentialsProperties(): void
    {
        $cerFile = $this->filePath('LAN7008173R5.cer.pem');
        $keyFile = $this->filePath('LAN7008173R5.key.pem');
        $passPhrase = trim($this->fileContents('LAN7008173R5.password'));
        $signature = 'Dw9fnvXKvDCy+oFqGNWG2ho1wcLaY4I9ddh5e+WqB5rfHbZEMyspuqQzYux2OL0U+g7arlx/w5imdQxjBlvrKgulX7'
            . 'K7HcHel60knsneDebEJNA0tyeTnJJn2e6DPd5GtxrLEHsjKtTGxl4p8QynX0x5uJoog09ZgIQ3adSq3cciH3FOfupiq9NbtMQ'
            . 'k9Da8ezI+pc5L0uu+mC9+RAR+r3agRkigGhGIeatS2QrA/B4FjZW2kzivz7J3zWEMm+JJMdYKzBoc7Us3aOS+kzuaz4T8+/yf'
            . 'IZy2qa9QEnxpOvk0Prh43LaObh9MKbu3uOnWaO3yMSuKE6DHZqmWtcO57A==';

        $credentials = new Credentials($cerFile, $keyFile, $passPhrase);

        $this->assertSame($cerFile, $credentials->certificate());
        $this->assertSame($keyFile, $credentials->privateKey());
        $this->assertSame($passPhrase, $credentials->passPhrase());

        $this->assertSame('LAN7008173R5', $credentials->rfc());
        $this->assertSame('20001000000300022815', $credentials->serialNumber());
        $this->assertStringStartsWith(
            'CN=A.C. 2 de pruebas(4096),O=Servicio de Administración Tributaria',
            $credentials->certificateIssuerName()
        );
        $this->assertSame($signature, base64_encode($credentials->sign('foo')));
    }

    public function testCreateCsdWithInvalidPassword(): void
    {
        $cerContent = $this->filePath('LAN7008173R5.cer.pem');
        $keyContent = $this->filePath('LAN7008173R5.key.pem');
        $passPhrase = 'this is sparta!';

        $credential = new Credentials($cerContent, $keyContent, $passPhrase);

        $this->expectException(CannotLoadCertificateAndPrivateKey::class);
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

        $this->expectException(CertificateIsNotCSD::class);
        $credential->certificateIssuerName(); // something that require csd creation
    }

    public function testCreateWithPhpCfdiCredential(): void
    {
        $cerFile = $this->filePath('LAN7008173R5.cer.pem');
        $keyFile = $this->filePath('LAN7008173R5.key.pem');
        $passPhrase = trim($this->fileContents('LAN7008173R5.password'));
        $phpCfdiCredential = Credential::openFiles($cerFile, $keyFile, $passPhrase);
        $credential = Credentials::createWithPhpCfdiCredential($phpCfdiCredential);
        $this->assertSame($phpCfdiCredential->rfc(), $credential->rfc());
    }
}
