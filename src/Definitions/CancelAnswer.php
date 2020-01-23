<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Definitions;

use Eclipxe\Enum\Enum;

/**
 * Define the answer to the cancellation request (accept/reject)
 *
 * @method static self accept()
 * @method static self reject()
 * @method bool isAccept()
 * @method bool isReject()
 */
class CancelAnswer extends Enum
{
    /**
     * @inheritDoc
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected static function overrideValues(): array
    {
        return [
            'accept' => 'Aceptacion',
            'reject' => 'Rechazo',
        ];
    }
}
