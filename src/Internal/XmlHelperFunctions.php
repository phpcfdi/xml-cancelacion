<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Internal;

use DOMDocument;
use DOMElement;
use PhpCfdi\XmlCancelacion\Exceptions\DocumentWithoutRootElement;

/** @internal */
trait XmlHelperFunctions
{
    /**
     * Get the document's root element. Throw an exception if not exists.
     *
     * @param DOMDocument $document
     * @return DOMElement
     * @throws DocumentWithoutRootElement
     */
    private function xmlDocumentElement(DOMDocument $document): DOMElement
    {
        $documentElement = $document->documentElement;
        if (null === $documentElement) {
            throw new DocumentWithoutRootElement();
        }
        return $documentElement;
    }
}
