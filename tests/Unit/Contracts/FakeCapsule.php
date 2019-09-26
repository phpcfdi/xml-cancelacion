<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit\Contracts;

use DOMDocument;
use PhpCfdi\XmlCancelacion\Contracts\CapsuleInterface;

class FakeCapsule implements CapsuleInterface
{
    /** @var string */
    private $rfc;

    public function __construct($rfc)
    {
        $this->rfc = $rfc;
    }

    public function exportToDocument(): DOMDocument
    {
        return (new FakeDocumentBuilder())->makeDocument($this);
    }

    public function belongsToRfc(string $rfc): bool
    {
        return ($rfc === $this->rfc);
    }
}
