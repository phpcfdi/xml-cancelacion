<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Models;

use Eclipxe\Enum\Enum;
use LogicException;

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

    public function xmlNamespaceCancellation(): string
    {
        if ($this->isCfdi()) {
            return 'http://cancelacfd.sat.gob.mx';
        }
        if ($this->isRetention()) {
            return 'http://www.sat.gob.mx/esquemas/retencionpago/1';
        }
        throw new LogicException('There is no xml namespace for the DocumentType'); // @codeCoverageIgnore
    }
}
