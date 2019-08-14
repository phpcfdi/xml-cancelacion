# CHANGELOG


## Version 0.4.1 2019-08-13

- Fix X509IssuerName to use certificate issuer as [RFC 4514](https://www.ietf.org/rfc/rfc4514.txt)
  according to [XML Signature Syntax and Processing Version 1.1](https://www.w3.org/TR/xmldsig-core1/)
  [4.5.4 The X509Data Element](https://www.w3.org/TR/xmldsig-core1/#sec-X509Data) where it states that:
  *The deprecated X509IssuerSerial element, which contains an X.509 issuer distinguished name/serial number pair.*
  *The distinguished name SHOULD be represented as a string that complies with section 3 of RFC4514, to be generated*
  *according to the [Distinguished Name Encoding Rules](https://www.w3.org/TR/xmldsig-core1/#dname-encrules) section*.
  Notice that in the encoding rules mention additional rules that __MAY__ be implemented. We are not. 


## Version 0.4.0 2019-08-13

- Drop dependence from `eclipxe/cfdiutils` to `phpcfdi/credentials`
- `PhpCfdi\XmlCancelacion\Credentials` changed from DTO to encapsulate certificate & private key logic:
    - uses internally `PhpCfdi\Credentials\Credential`
    - offers methods to extract any data or execute any action from certificate or private key
- Minor improvements in documentation


## Version 0.3.0 2019-06-27

- Fix issue when calling `DOMDocument::createElement`/`DOMDocument::createElementNS` and content has an empersand `&`:
    - `KeyInfo/X509Data/X509IssuerSerial/X509IssuerName`
    - `Folios/UUID`
    - Other uses on `createElement[NS]` are not important since they cannot have any ampersand.
- Depends on `eclipxe/cfdiutils` version 2.10.4 to use `Xml::createElement` & `Xml::createElementNS`.
- Move certificate extracting info from `DOMSigner::createKeyInfo` to `DOMSigner::sign`
- Change signature of `DOMSigner::createKeyInfo(Certificado)` to
  `DOMSigner::createKeyInfoElement(string $issuerName, string $serialNumber, string $pemContents)`, we can test it now.
- Extract `DOMSigner::createKeyValueFromCertificado` to `DOMSigner::createKeyValueElement` to allow testing.
- Remove protected method `DOMSigner::createKeyValueFromCertificado` in favor of `DOMSigner::createKeyValueElement`.


## Version 0.2.1 2019-05-13

- Release with new tag since v0.2.0 did not include this changes


## Version 0.2.0 2019-05-13

- Code quality improvements thanks to phpstan and infection (mutation testing framework)
- Remove `Capsule::append` method, `Capsule` is now immutable.
- Throw `LogicException` when `DOMSigner` uses a document that does not have root element
- `Capsule` now implements `Countable`


## Version 0.1.0 2019-04-09

- First public version
