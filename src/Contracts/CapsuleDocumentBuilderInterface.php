<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Contracts;

use DOMDocument;

interface CapsuleDocumentBuilderInterface
{
    /**
     * Build and return a DOMDocument with the capsule data
     *
     * @param CapsuleInterface $capsule
     * @return DOMDocument
     */
    public function makeDocument(CapsuleInterface $capsule): DOMDocument;
}
