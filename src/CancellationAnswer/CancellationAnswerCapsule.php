<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\CancellationAnswer;

use DateTimeImmutable;
use DOMDocument;
use PhpCfdi\XmlCancelacion\Contracts\CapsuleInterface;
use PhpCfdi\XmlCancelacion\Definitions\CancellationAnswer;

class CancellationAnswerCapsule implements CapsuleInterface
{
    /** @var string */
    private $uuid;

    /** @var CancellationAnswer */
    private $answer;

    /** @var string */
    private $rfc;

    /** @var DateTimeImmutable */
    private $dateTime;

    /** @var string */
    private $pacRfc;

    public function __construct(
        string $rfc,
        string $uuid,
        CancellationAnswer $answer,
        string $pacRfc,
        DateTimeImmutable $dateTime
    ) {
        $this->rfc = $rfc;
        $this->uuid = $uuid;
        $this->answer = $answer;
        $this->dateTime = $dateTime;
        $this->pacRfc = $pacRfc;
    }

    public function rfc(): string
    {
        return $this->rfc;
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function answer(): CancellationAnswer
    {
        return $this->answer;
    }

    public function dateTime(): DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function pacRfc(): string
    {
        return $this->pacRfc;
    }

    public function exportToDocument(): DOMDocument
    {
        return (new CancellationAnswerDocumentBuilder())->makeDocument($this);
    }

    public function belongsToRfc(string $rfc): bool
    {
        return ($rfc === $this->rfc());
    }
}
