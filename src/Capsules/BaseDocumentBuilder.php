<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Capsules;

use DOMDocument;

/**
 * Helper class to create the basic structure of a DOMDocument to be signed
 *
 * It provides the logic to append additional namespaces and create a base DOMDocument
 * with those namespaces and a document root element
 *
 * It is commonly used by DocumentBuilders
 */
class BaseDocumentBuilder
{
    private const XMLDOC_NO_PRESERVE_WHITESPACE = false;

    private const XMLDOC_NO_FORMAT_OUTPUT = false;

    /** @var array<string, string> */
    private readonly array $extraNamespaces;

    /**
     * CapsuleSigner constructor.
     *
     * If $extraNamespaces is null then it will use default extra namespaces,
     * but if it is defined it will only use what is defined on parameter
     *
     * @param array<string, string>|null $extraNamespaces
     */
    public function __construct(?array $extraNamespaces = null)
    {
        if (null === $extraNamespaces) {
            $extraNamespaces = static::defaultExtraNamespaces();
        }
        $this->extraNamespaces = $extraNamespaces;
    }

    /** @return array<string, string> */
    public function extraNamespaces(): array
    {
        return $this->extraNamespaces;
    }

    /** @return array<string, string> */
    public static function defaultExtraNamespaces(): array
    {
        return [
            'xsd' => 'http://www.w3.org/2001/XMLSchema',
            'xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
        ];
    }

    public function createBaseDocument(string $tagName, string $namespace): DOMDocument
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->preserveWhiteSpace = self::XMLDOC_NO_PRESERVE_WHITESPACE;
        $document->formatOutput = self::XMLDOC_NO_FORMAT_OUTPUT;
        $cancelacion = $document->createElementNS($namespace, $tagName);
        foreach ($this->extraNamespaces() as $prefix => $uri) {
            $cancelacion->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:' . $prefix, $uri);
        }
        $document->appendChild($cancelacion);
        return $document;
    }
}
