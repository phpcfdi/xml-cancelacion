<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Models;

use Eclipxe\Enum\Enum;

/**
 * Define if the Rfc should be used as Issuer or Receiver
 *
 * @method static self issuer()
 * @method static self receiver()
 * @method bool isIssuer()
 * @method bool isReceiver()
 */
class RfcRole extends Enum
{
}
