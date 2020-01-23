<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit\Capsules;

use DOMDocument;
use PhpCfdi\XmlCancelacion\Capsules\BaseDocumentBuilder;
use PhpCfdi\XmlCancelacion\Capsules\CapsuleInterface;

class FakeCapsule implements CapsuleInterface
{
    /** @var string */
    private $rfc;

    public function __construct(string $rfc)
    {
        $this->rfc = $rfc;
    }

    public function rfc(): string
    {
        return $this->rfc;
    }

    public function exportToDocument(): DOMDocument
    {
        return (new BaseDocumentBuilder())->createBaseDocument('fake', 'http://tempuri.org/fake');
    }

    public function belongsToRfc(string $rfc): bool
    {
        return ($rfc === $this->rfc());
    }
}
