<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit;

use DateTimeImmutable;
use LogicException;
use PhpCfdi\XmlCancelacion\Capsules\Cancellation;
use PhpCfdi\XmlCancelacion\Capsules\CapsuleInterface;
use PhpCfdi\XmlCancelacion\Models\CancelDocuments;
use PhpCfdi\XmlCancelacion\Models\DocumentType;
use PhpCfdi\XmlCancelacion\XmlCancelacionHelper;

final class XmlCancelacionHelperSpy extends XmlCancelacionHelper
{
    private ?Cancellation $lastCancellation = null;

    private ?CapsuleInterface $lastSignedCapsule = null;

    protected function createCancellationObject(
        CancelDocuments $documents,
        ?DateTimeImmutable $dateTime,
        DocumentType $type
    ): Cancellation {
        $this->lastCancellation = parent::createCancellationObject(
            $documents,
            $dateTime,
            $type
        );
        return $this->lastCancellation;
    }

    public function getLastCancellation(): Cancellation
    {
        if (null === $this->lastCancellation) {
            throw new LogicException('Must call a method that creates a cancellation object first');
        }
        return $this->lastCancellation;
    }

    public function signCapsule(CapsuleInterface $capsule): string
    {
        $this->lastSignedCapsule = $capsule;
        return parent::signCapsule($capsule);
    }

    public function getLastSignedCapsule(): CapsuleInterface
    {
        if (null === $this->lastSignedCapsule) {
            throw new LogicException('Must call a method that set a last signed capsule first');
        }
        return $this->lastSignedCapsule;
    }
}
