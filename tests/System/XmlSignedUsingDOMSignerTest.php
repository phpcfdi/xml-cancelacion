<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Tests\System;

use DateTimeImmutable;
use PhpCfdi\XmlCancelacion\Capsules\Cancellation;
use PhpCfdi\XmlCancelacion\Credentials;
use PhpCfdi\XmlCancelacion\Models\CancelDocument;
use PhpCfdi\XmlCancelacion\Models\CancelDocuments;
use PhpCfdi\XmlCancelacion\Signers\DOMSigner;
use PhpCfdi\XmlCancelacion\Tests\TestCase;

final class XmlSignedUsingDOMSignerTest extends TestCase
{
    /** @var DOMSigner */
    private $domSigner;

    public function setUp(): void
    {
        parent::setUp();

        $credentials = new Credentials(
            $this->filePath('EKU9003173C9.cer.pem'),
            $this->filePath('EKU9003173C9.key.pem'),
            trim($this->fileContents('EKU9003173C9.password'))
        );

        $capsule = new Cancellation(
            'EKU9003173C9',
            new CancelDocuments(CancelDocument::newWithErrorsUnrelated('62B00C5E-4187-4336-B569-44E0030DC729')),
            new DateTimeImmutable('2022-01-06 17:49:12')
        );

        $document = $capsule->exportToDocument();

        $signer = new DOMSigner();
        $signer->signDocument($document, $credentials);

        $this->domSigner = $signer;
    }

    public function testCreatedValues(): void
    {
        // signature text for preset capsule *must* be the following, see not used xmlns declarations
        /** @noinspection XmlUnusedNamespaceDeclaration */
        $expectedDigestSource = '<Cancelacion xmlns="http://cancelacfd.sat.gob.mx"'
            . ' xmlns:xsd="http://www.w3.org/2001/XMLSchema"'
            . ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'
            . ' Fecha="2022-01-06T17:49:12" RfcEmisor="EKU9003173C9">'
            . '<Folios>'
            . '<Folio FolioSustitucion="" Motivo="02" UUID="62B00C5E-4187-4336-B569-44E0030DC729"></Folio>'
            . '</Folios>'
            . '</Cancelacion>';

        $expectedDigestValue = 'C5CrlWmW2k+LRbwIz2JTydPW2+g=';

        // signed info text for preset capsule *must* be the following, see not used xmlns declarations and C14N
        /** @noinspection XmlUnusedNamespaceDeclaration */
        $expectedSignedInfo = '<SignedInfo xmlns="http://www.w3.org/2000/09/xmldsig#"'
            . ' xmlns:xsd="http://www.w3.org/2001/XMLSchema"'
            . ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            . '<CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315">'
            . '</CanonicalizationMethod>'
            . '<SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1"></SignatureMethod>'
            . '<Reference URI="">'
            . '<Transforms>'
            . '<Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"></Transform>'
            . '</Transforms>'
            . '<DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1"></DigestMethod>'
            . '<DigestValue>C5CrlWmW2k+LRbwIz2JTydPW2+g=</DigestValue>'
            . '</Reference>'
            . '</SignedInfo>';

        $expectedSignedValue = implode('', [
            'Kxm+BjKx10C/G3c8W8IItAXgdxKP1hmBf2F4DnVcPLTKNfvRu/E29NG2PXDcXGUauAOLi13+7BT2',
            'ovURHQKNsjErmAD5Ya09gkUHNstg8ja6K3O5haTNWSIGGf1ZGi1fY8pZ/VSL32L1BnJsu3d81tnx',
            'npriSWkqSQHG2xcll9L2qxdjxlhPfllL1D9nF1TrCv6QCGzgmnRXs6sgUz7Zb2nZaJzPPnausykt',
            'Es56LnQr+dpgGs12G8X4NyqFVo8byNA5/fSwF6WLl7RN4p9fKI1WGZg93yHLG6R1fZ+80N0vebNm',
            'RDJCHnTrO2aLOn1dkneCqBExOzj8hJMWljzWGQ==',
        ]);

        $this->assertSame($expectedDigestSource, $this->domSigner->getDigestSource());
        $this->assertSame($expectedDigestValue, $this->domSigner->getDigestValue());
        $this->assertSame($expectedSignedInfo, $this->domSigner->getSignedInfoSource());
        $this->assertSame($expectedSignedValue, $this->domSigner->getSignedInfoValue());
    }
}
