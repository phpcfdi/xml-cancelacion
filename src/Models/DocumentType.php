<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Models;

use Eclipxe\Enum\Enum;

/**
 * Define the document type (cfdi or retention)
 *
 * @method static self cfdi()
 * @method static self retention()
 * @method bool isCfdi()
 * @method bool isRetention()
 */
final class DocumentType extends Enum
{
    /** @noinspection PhpMissingParentCallCommonInspection */
    protected static function overrideValues(): array
    {
        return [
            'cfdi' => 'http://cancelacfd.sat.gob.mx',
            'retention' => 'http://cancelaretencion.sat.gob.mx',
        ];
    }
}
