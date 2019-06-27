# CHANGELOG

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
- Remove protected method `DOMSigner::createKeyValueFromCertificado` in favor of `DOMSigner::createKeyValueFromPemContents`.


## Version 0.2.1 2019-05-13

- Release with new tag since v0.2.0 did not include this changes


## Version 0.2.0 2019-05-13

- Code quality improvements thanks to phpstan and infection (mutation testing framework)
- Remove `Capsule::append` method, `Capsule` is now immutable.
- Throw `LogicException` when `DOMSigner` uses a document that does not have root element
- `Capsule` now implements `Countable`


## Version 0.1.0 2019-04-09

- First public version
