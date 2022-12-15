<?php

declare(strict_types=1);

namespace PhpCfdi\XmlCancelacion\Signers;

use DOMDocument;
use DOMElement;
use Exception;
use LogicException;
use PhpCfdi\XmlCancelacion\Credentials;
use PhpCfdi\XmlCancelacion\Internal\XmlHelperFunctions;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;

class XmlSecLibsSigner implements SignerInterface
{
    use XmlHelperFunctions;
    use CreateKeyInfoElementTrait;
    use SignCapsuleMethodTrait;

    public function signDocument(DOMDocument $document, Credentials $credentials): void
    {
        $this->xmlDocumentElement($document);

        try {
            // move XmlSecLibs signature to internal method
            $sigNode = $this->signDocumentInternal($document, $credentials);
        } catch (Exception $xmlSecLibsException) {
            throw new LogicException('Cannot create signature using XmlSecLibs', 0, $xmlSecLibsException);
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
    }

    /**
     * @param DOMDocument $document
     * @param Credentials $credentials
     * @return DOMElement Signature node
     * @throws Exception
     */
    protected function signDocumentInternal(DOMDocument $document, Credentials $credentials): DOMElement
    {
        // use a modified version of XMLSecurityDSig that does not contain xml white-spaces
        $objDSig = new class () extends XMLSecurityDSig {
            /** @noinspection PhpMissingParentConstructorInspection */
            public function __construct()
            {
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

        $rootElement = $this->xmlDocumentElement($document);
        // Sign the XML file, set the second parameter to document element,
        // if second parameter is empty it will remove extra namespaces
        $objDSig->sign($objKey, $rootElement);

        assert(
            $objDSig->sigNode instanceof DOMElement,
            new LogicException('XMLSecurityDSig->$sigNode should be a DOMElement')
        );
        return $objDSig->sigNode;
    }
}
