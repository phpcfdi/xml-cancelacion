<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit;

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
        $signer = new class(new DOMDocument()) extends DOMSigner {
            public function exposeObtainPublicKeyValues(string $publicKey): array
            {
                return $this->obtainPublicKeyValues($publicKey);
            }
        };

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot read public key from certificate');
        $signer->exposeObtainPublicKeyValues('BAD PUBLIC KEY');
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
