<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion;

use DOMDocument;
use DOMElement;
use LogicException;
use PhpCfdi\XmlCancelacion\Contracts\SignerInterface;
use PhpCfdi\XmlCancelacion\Exceptions\DocumentWithoutRootElement;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;

class XmlSecLibsSigner implements SignerInterface
{
    use CreateKeyInfoElementTrait;
    use SignCapsuleMethodTrait;

    public function signDocument(DOMDocument $document, Credentials $credentials): void
    {
        $rootElement = $document->documentElement;
        if (! $rootElement instanceof DOMElement) {
            throw new DocumentWithoutRootElement();
        }

        // use a mofidied version of XMLSecurityDSig that does not contains xml white-spaces
        $objDSig = new class('') extends XMLSecurityDSig {
            public function __construct($prefix = 'ds')
            {
                parent::__construct($prefix);
                // set sigNode property with a signature node without inner spaces
                $document = new DOMDocument();
                $document->appendChild($signature = $document->createElementNS(parent::XMLDSIGNS, 'Signature'))
                    ->appendChild($document->createElementNS(parent::XMLDSIGNS, 'SignedInfo'))
                    ->appendChild($document->createElementNS(parent::XMLDSIGNS, 'SignatureMethod'));
                $this->sigNode = $signature;
            }
        };

        // Use the c14n inclusive canonicalization without comments
        $objDSig->setCanonicalMethod(XMLSecurityDSig::C14N);
        // Sign using SHA1
        $objDSig->addReference(
            $document,
            XMLSecurityDSig::SHA1,
            ['http://www.w3.org/2000/09/xmldsig#enveloped-signature'],
            ['force_uri' => true]
        );

        // Create a new (private) Security key
        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, ['type' => 'private']);
        $objKey->passphrase = $credentials->passPhrase();
        $objKey->loadKey($credentials->privateKey(), true);

        // Sign the XML file, set the second parameter to document element,
        // if second parameter is empty it will remove extra namespaces
        $objDSig->sign($objKey, $rootElement);
        $sigNode = $objDSig->sigNode;
        if (! $sigNode instanceof DOMElement) {
            throw new LogicException('Signature node does not exists after sign');
        }

        // create the KeyInfo element using own procedure
        // the procedure from XMLSecLibs does not include correct format of issuer name,
        // correct format of serial number and does not include public key data
        $keyInfoElement = $this->createKeyInfoElement(
            $document,
            $credentials->certificateIssuerName(),
            $credentials->serialNumber(),
            $credentials->certificateAsPEM(),
            $credentials->publicKeyData()
        );
        $sigNode->appendChild($keyInfoElement);

        // Append the signature to the root element
        $objDSig->appendSignature($rootElement);
    }
}
