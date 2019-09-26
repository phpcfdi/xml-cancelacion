<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\Unit\Contracts;

use DOMDocument;
use PhpCfdi\XmlCancelacion\Contracts\AbstractCapsuleDocumentBuilder;
use PhpCfdi\XmlCancelacion\Contracts\CapsuleInterface;

class FakeDocumentBuilder extends AbstractCapsuleDocumentBuilder
{
    /**
     * @param CapsuleInterface&FakeCapsule $capsule
     * @return DOMDocument
     */
    public function makeDocument(CapsuleInterface $capsule): DOMDocument
    {
        $this->assertCapsuleType($capsule, FakeCapsule::class);
        return $this->createBaseDocument('fake', 'http://tempuri.org/fake');
    }
}
