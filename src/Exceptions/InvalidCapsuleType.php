<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Exceptions;

use PhpCfdi\XmlCancelacion\Contracts\CapsuleInterface;

class InvalidCapsuleType extends XmlCancelacionLogicException
{
    /** @var CapsuleInterface */
    private $capsule;

    /** @var string */
    private $expected;

    public function __construct(CapsuleInterface $capsule, string $expected)
    {
        parent::__construct(sprintf('Given capsule %s is not expected type %s', get_class($capsule), $expected));
        $this->capsule = $capsule;
        $this->expected = $expected;
    }

    public function getCapsule(): CapsuleInterface
    {
        return $this->capsule;
    }

    public function getExpected(): string
    {
        return $this->expected;
    }
}
