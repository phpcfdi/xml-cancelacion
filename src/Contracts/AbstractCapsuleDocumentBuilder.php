<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Contracts;

use DOMDocument;
use PhpCfdi\XmlCancelacion\Exceptions\InvalidCapsuleType;

/**
 * Helper class to be extended and implement in fewer steps a CapsuleDocumentBuilderInterface
 * This class provides the logig to append additional namespaces and create a base DOMDocument
 * with those namespaces and a document root element
 */
abstract class AbstractCapsuleDocumentBuilder implements CapsuleDocumentBuilderInterface
{
    /** @var array<string, string> */
    private $extraNamespaces;

    /**
     * CapsuleSigner constructor.
     *
     * If $extraNamespaces is null then it will use default extra namespaces,
     * but if it is defined it will only use what is defined on parameter
     *
     * @param array<string, string>|null $extraNamespaces
     */
    public function __construct(array $extraNamespaces = null)
    {
        if (null === $extraNamespaces) {
            $extraNamespaces = static::defaultExtraNamespaces();
        }
        $this->extraNamespaces = $extraNamespaces;
    }

    public function extraNamespaces(): array
    {
        return $this->extraNamespaces;
    }

    /**
     * @return array<string| string>
     */
    public static function defaultExtraNamespaces(): array
    {
        return [
            'xsd' => 'http://www.w3.org/2001/XMLSchema',
            'xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
        ];
    }

    protected function assertCapsuleType(CapsuleInterface $capsule, string $expectedClassName): void
    {
        if (! $capsule instanceof $expectedClassName) {
            throw new InvalidCapsuleType($capsule, $expectedClassName);
        }
    }

    protected function createBaseDocument(string $tagName, string $namespace): DOMDocument
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->preserveWhiteSpace = false;
        $document->formatOutput = false;
        $cancelacion = $document->createElementNS($namespace, $tagName);
        foreach ($this->extraNamespaces() as $prefix => $uri) {
            $cancelacion->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:' . $prefix, $uri);
        }
        $document->appendChild($cancelacion);
        return $document;
    }
}
