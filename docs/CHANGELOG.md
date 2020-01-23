# CHANGELOG

# UNRELEASED

- Update license year, happy 2020!
- Include cancellation document for document *"CFDI de retención e información de pagos"*.
- Add `DocumentType` enumerator with keys `cfdi` and `retention` to specify the correct namespace of the request.
- Add `XmlCancellationHelper::signRetentionCancellation` and `XmlCancellationHelper::signRetentionCancellationUuids`
  that create cancellation request for retentions.
- Refactor `XmlCancelacionHelper` and delegate the creation of the `Cancellation` object to a specific
  protected method.
- Other capsules uses also `DocumentType` to set the main namespace but as hardcoded.
- Development:
    - Move from `phpstan/phpstan-shim` to `phpstan/phpstan`.
    - Upgrade to `phpstan/phpstan: ^0.12`
- Testing:
    - Improve `CancellationTest` to check that `DocumentType` is used correctly.
    - Create a testing class `XmlCancelacionHelperSpy` to spy on `XmlCancelacionHelper`.
    - Refactor tests on `XmlCancelacionHelperTest` to test against the spy class.  

# Version 1.0.1 2019-10-02

- Fix documentation to point out version 1.0 instead of 0.5.
- Fix documentation PHP examples.

# Version 1.0.0 2019-09-28

- This version is a major change, it is not compatible with previous versions
  Read [UPGRADE-1.0](https://github.com/phpcfdi/xml-cancelacion/blob/master/docs/UPGRADE-1.0.md)
- New signed documents:
    - Cancellation: For request to SAT a cancellation of one or many CFDI.
    - ObtainRelated: For asking to SAT related documents of a CFDI.
    - CancellationAnswer: For setting the answer to SAT about a cancellation request.
- New project concepts:
    - Capsule: DTO that contains source data, also implements `CapsuleInterface`
    - Signer: Manipulation object implementing `SignerInterface`, takes a DOMDocument and append signature data
- New general process:
    - A `CapsuleInterface` is exported as a `DOMDocument`
    - Using a `SignerInterface` the `DOMDocument` is signed and signature is appended.
    - Signature is exported from `DOMDocument::saveXml()`
- New helper `XmlCancelacionHelper`:
    - If no `SignerInterface` is provided then the default is used
- Credentials can be created from `PhpCfdi\Credentials\Credential`
- Exceptions are specific to this library:
    - `XmlCancelacionException`
        - `XmlCancelacionLogicException` extends `LogicException`
            - `DocumentWithoutRootElement`
            - `HelperDoesNotHaveCredentials`
        - `XmlCancelacionRuntimeException` extends `RuntimeException`
            - `CannotLoadCertificateAndPrivateKey`
            - `CapsuleRfcDoesnotBelongToCertificateRfc`
            - `CertificateIsNotCSD`


## Version 0.4.2 2019-09-05

- Include a helper object `XmlCancelacionHelper` that simplify working with this library,
  see [README](https://github.com/phpcfdi/xml-cancelacion/blob/master/README.md) for usage.
- Other minimal changes on documentation.


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
