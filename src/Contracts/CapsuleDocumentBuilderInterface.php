<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Contracts;

use DOMDocument;
use PhpCfdi\XmlCancelacion\Exceptions\InvalidCapsuleType;

interface CapsuleDocumentBuilderInterface
{
    /**
     * Build and return a DOMDocument with the capsule data
     *
     * @param CapsuleInterface $capsule
     * @return DOMDocument
     * @throws InvalidCapsuleType
     */
    public function makeDocument(CapsuleInterface $capsule): DOMDocument;
}
