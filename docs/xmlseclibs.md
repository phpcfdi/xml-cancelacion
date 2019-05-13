
```php
<?php
use PhpCfdi\XmlCancelacion\Credentials;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;

class XMLSecurityDSigSigner
{
    public function addSignatureToDocumentXmlSec(DOMDocument $document, Credentials $signObjects): void
    {
        // creación del nodo a firmar ???
        $objDsig = new XMLSecurityDSig(''); // no prefix
        $objDsig->setCanonicalMethod(XMLSecurityDSig::C14N);
        $objDsig->addReference(
            $document,
            XMLSecurityDSig::SHA1,
            ['http://www.w3.org/2000/09/xmldsig#enveloped-signature'],
            ['force_uri' => true]
        );

        // abrir la llave privada y generar la firma
        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, ['type' => 'private']);
        $objKey->passphrase = $signObjects->passPhrase();
        $objKey->loadKey($signObjects->privateKey(), true);
        $objDsig->sign($objKey, $document->documentElement);

        // agregar el certificado!
        $objDsig->add509Cert('file://' . $signObjects->certificate(), true, true, [
            'issuerSerial' => true,
            'subjectName' => false,
            'pubKeyInfo' => true,
        ]);
        $objDsig->appendToKeyInfo($this->createKeyValue($document, $signObjects->certificate()));

        // agregar la firma al nodo raíz del documento
        $objDsig->appendSignature($document->documentElement);
    }

    public function createKeyValue(DOMDocument $document, string $certificateFile): DOMElement
    {
        $pubKeyData = $this->obtainPublicKeyData(file_get_contents($certificateFile));
        $keyValue = $document->createElement('KeyValue');
        if (OPENSSL_KEYTYPE_RSA === $pubKeyData['type']) {
            $rsaKeyValue = $keyValue->appendChild($document->createElement('RSAKeyValue'));
            $rsaKeyValue->appendChild($document->createElement('Modulus', base64_encode($pubKeyData['rsa']['n'])));
            $rsaKeyValue->appendChild($document->createElement('Exponent', base64_encode($pubKeyData['rsa']['e'])));
        }

        return $keyValue;
    }
    
    protected function obtainPublicKeyData(string $publicKeyContent): array
    {
        $pubKey = openssl_get_publickey($publicKeyContent);
        if (! is_resource($pubKey)) {
            throw new RuntimeException('Cannot read public key from certificate');
        }
        $pubKeyData = openssl_pkey_get_details($pubKey) ?: [];
        openssl_free_key($pubKey);
        
        return $pubKeyData;
    }
}
```
