<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Capsules;

use DOMDocument;

interface CapsuleInterface
{
    public function exportToDocument(): DOMDocument;

    public function belongsToRfc(string $rfc): bool;
}
