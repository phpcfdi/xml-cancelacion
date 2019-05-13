<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit;

use CfdiUtils\Certificado\Certificado;
use DOMDocument;
use DOMElement;
use LogicException;
use PhpCfdi\XmlCancelacion\Credentials;
use PhpCfdi\XmlCancelacion\DOMSigner;
use PhpCfdi\XmlCancelacion\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;

class DOMSignerTest extends TestCase
{
    public function testThrowExceptionWhenCannotGetPublicKeyFromCertificate(): void
    {
        /** @var Certificado&MockObject $certificate */
        $certificate = $this->createMock(Certificado::class);
        $certificate->method('getPemContents')->willReturn('BAD KEY');

        $signer = new class(new DOMDocument()) extends DOMSigner {
            public function exposeCreateKeyValueFromCertificado(Certificado $certificate): DOMElement
            {
                return $this->createKeyValueFromCertificado($certificate);
            }
        };

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot read public key from certificate');
        $signer->exposeCreateKeyValueFromCertificado($certificate);
    }

    public function testThrowExceptionWhenPassingAnEmptyDomDocument(): void
    {
        /** @var Credentials&MockObject $credentials */
        $credentials = $this->createMock(Credentials::class);
        $signer = new DOMSigner(new DOMDocument());

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Document does not have a root element');
        $signer->sign($credentials);
    }
}
