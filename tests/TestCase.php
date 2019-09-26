<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests;

use DOMDocument;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public static function filePath(string $filename): string
    {
        return __DIR__ . '/_files/' . $filename;
    }

    public static function fileContents(string $filename): string
    {
        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        return @file_get_contents(static::filePath($filename)) ?: '';
    }

    public function xmlWithoutWhitespace(string $contents): string
    {
        $document = new DOMDocument();
        $document->preserveWhiteSpace = false;
        $document->formatOutput = false;
        $document->loadXML($contents);
        return $document->saveXml();
    }
}
