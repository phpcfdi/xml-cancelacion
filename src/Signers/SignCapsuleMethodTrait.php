<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Signers;

use PhpCfdi\XmlCancelacion\Capsules\CapsuleInterface;
use PhpCfdi\XmlCancelacion\Credentials;
use PhpCfdi\XmlCancelacion\Exceptions\CapsuleRfcDoesnotBelongToCertificateRfc;

trait SignCapsuleMethodTrait
{
    public function signCapsule(CapsuleInterface $capsule, Credentials $credentials): string
    {
        if (! $capsule->belongsToRfc($credentials->rfc())) {
            throw new CapsuleRfcDoesnotBelongToCertificateRfc($capsule, $credentials->rfc());
        }
        $document = $capsule->exportToDocument();
        $this->signDocument($document, $credentials);
        return (string) $document->saveXML();
    }
}
