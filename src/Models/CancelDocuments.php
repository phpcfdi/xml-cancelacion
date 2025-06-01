<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Models;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Collection of documents to cancel
 *
 * @implements IteratorAggregate<int, CancelDocument>
 */
final class CancelDocuments implements IteratorAggregate, Countable
{
    /** @var list<CancelDocument> */
    private readonly array $documents;

    /** @var int<0, max> */
    private readonly int $count;

    public function __construct(CancelDocument ...$documents)
    {
        $this->documents = array_values($documents);
        $this->count = count($documents);
    }

    /**
     * The list of UUIDS
     * @return string[]
     */
    public function uuids(): array
    {
        return array_map(
            static fn (CancelDocument $document): string => $document->uuid()->getValue(),
            $this->documents
        );
    }

    /** @return Traversable<int, CancelDocument> */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->documents);
    }

    public function count(): int
    {
        return $this->count;
    }
}
