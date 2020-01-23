<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Definitions;

use Eclipxe\Enum\Enum;

/**
 * Define the answer to the cancellation request (accept/reject)
 *
 * @method static self cfdi()
 * @method static self retention()
 * @method bool isCfdi()
 * @method bool isRetention()
 */
final class DocumentType extends Enum
{
    /**
     * @inheritDoc
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected static function overrideValues(): array
    {
        return [
            'cfdi' => 'http://cancelacfd.sat.gob.mx',
            'retention' => 'http://cancelaretencion.sat.gob.mx',
        ];
    }
}
