<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion;

use DateTimeImmutable;
use PhpCfdi\XmlCancelacion\Cancellation\CancellationCapsule;

/** @deprecated */
class Capsule extends CancellationCapsule
{
    public function __construct(string $rfc, array $uuids = [], DateTimeImmutable $date = null)
    {
        parent::__construct($rfc, $uuids, $date);
        trigger_error(sprintf('Deprecated class since 0.5.0, use %s', CancellationCapsule::class), E_USER_DEPRECATED);
    }
}
