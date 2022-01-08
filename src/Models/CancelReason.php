<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Models;

use Eclipxe\Enum\Enum;

/**
 * Define the cancellation reason
 *
 * @method static self withErrorsRelated()
 * @method static self withErrorsUnrelated()
 * @method static self notExecuted()
 * @method static self normativeToGlobal()
 * @method bool isWithErrorsRelated()
 * @method bool isWithErrorsUnrelated()
 * @method bool isNotExecuted()
 * @method bool isNormativeToGlobal()
 */
final class CancelReason extends Enum
{
    /** @noinspection PhpMissingParentCallCommonInspection */
    protected static function overrideValues(): array
    {
        return [
            'withErrorsRelated' => '01',
            'withErrorsUnrelated' => '02',
            'notExecuted' => '03',
            'normativeToGlobal' => '04',
        ];
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    protected static function overrideIndices(): array
    {
        return [
            'withErrorsRelated' => 1,
            'withErrorsUnrelated' => 2,
            'notExecuted' => 3,
            'normativeToGlobal' => 4,
        ];
    }
}
