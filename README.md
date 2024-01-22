# phpcfdi/xml-cancelacion

[![Source Code][badge-source]][source]
[![Packagist PHP Version Support][badge-php-version]][php-version]
[![Discord][badge-discord]][discord]
[![Latest Version][badge-release]][release]
[![Software License][badge-license]][license]
[![Build Status][badge-build]][build]
[![Reliability][badge-reliability]][reliability]
[![Maintainability][badge-maintainability]][maintainability]
[![Code Coverage][badge-coverage]][coverage]
[![Violations][badge-violations]][violations]
[![Total Downloads][badge-downloads]][downloads]

> Genera documentos de cancelación de CFDI firmados (XMLSEC)

:us: The documentation of this project is in spanish as this is the natural language for intended audience.

Esta librería contiene el código necesario para crear una solicitud de cancelación acorde al SAT.
Esta solicitud está descrita en el Anexo 20, y solo es accesible por medio de un PAC.

Algunos PAC ofrecen métodos de cancelación que recaen en la fabricación de esta firma,
de esta manera no es necesario compartir el certificado ni la llave privada con el PAC.

- Siempre que tu PAC ofrezca un método de cancelación basado en el XML deberías usarlo.
- Si tu PAC no lo ofrece entonces deberías solicitárselo.
- Nunca compartas tu llave privada de firmado de CFDI con nadie, ni con tu PAC.

## Instalación

Usa [composer](https://getcomposer.org/)

```shell
composer require phpcfdi/xml-cancelacion
```

## Ejemplo básico de uso

### Con el objeto de ayuda

```php
<?php
declare(strict_types=1);
use PhpCfdi\XmlCancelacion\Models\CancelAnswer;
use PhpCfdi\XmlCancelacion\Models\CancelDocument;
use PhpCfdi\XmlCancelacion\Models\RfcRole;
use PhpCfdi\XmlCancelacion\XmlCancelacionHelper;

$xmlCancelacion = new XmlCancelacionHelper();

$solicitudCancelacion = $xmlCancelacion
    ->setNewCredentials('certificado.cer', 'llaveprivada.key', 'contraseña')
    ->signCancellation(CancelDocument::newNotExecuted('11111111-2222-3333-4444-000000000001'));

$consultaRelacionados = $xmlCancelacion->signObtainRelated(
    '11111111-2222-3333-4444-000000000002', // uuid a consultar
    RfcRole::issuer(), // emitido por el rfc de la credencial
    'CVD110412TF6' // RFC del PAC (Quadrum & Finkok)
);

$consultaRelacionados = $xmlCancelacion->signCancellationAnswer(
    '11111111-2222-3333-4444-000000000002', // uuid a responder
    CancelAnswer::accept(), // aceptar la cancelación
    'CVD110412TF6' // RFC del PAC (Quadrum & Finkok)
);
```

### Con un uso detallado de solicitud de cancelación

```php
<?php
declare(strict_types=1);
use PhpCfdi\XmlCancelacion\Capsules\Cancellation;
use PhpCfdi\XmlCancelacion\Credentials;
use PhpCfdi\XmlCancelacion\Models\CancelDocument;
use PhpCfdi\XmlCancelacion\Models\CancelDocuments;
use PhpCfdi\XmlCancelacion\Signers\DOMSigner;

// certificado, llave privada y clave de llave
$credentials = new Credentials('certificado.cer.pem', 'privatekey.key.pem', '12345678a');

// datos de cancelación
$data = new Cancellation(
    'EKU9003173C9',
    new CancelDocuments(CancelDocument::newWithErrorsUnrelated('62B00C5E-4187-4336-B569-44E0030DC729')),
    new DateTimeImmutable()
);

// generación del xml
$xml = (new DOMSigner())->signCapsule($data, $credentials);
```

La salida esperada es algo como lo siguiente (sin los espacios en blanco, que agregué para mejor lectura).

```xml
<?xml version="1.0" encoding="UTF-8"?>
<Cancelacion xmlns="http://cancelacfd.sat.gob.mx"
             xmlns:xsd="http://www.w3.org/2001/XMLSchema"
             xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
             RfcEmisor="EKU9003173C9" Fecha="2022-01-06T17:49:12">
    <Folios>
        <Folio UUID="62B00C5E-4187-4336-B569-44E0030DC729" Motivo="02"></Folio>
    </Folios>
    <Signature xmlns="http://www.w3.org/2000/09/xmldsig#">
        <SignedInfo>
            <CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315"/>
            <SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1"/>
            <Reference URI="">
                <Transforms>
                    <Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"/>
                </Transforms>
                <DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1"/>
                <DigestValue>C5CrlWmW2k+LRbwIz2JTydPW2+g=</DigestValue>
            </Reference>
        </SignedInfo>
        <SignatureValue>Kxm+BjKx10C/G3c8W8IItAXgdxKP1hmBf2F4DnVcPLTKNfvRu/E29NG2PXDcXGUauAOLi13+7BT2ovURHQKNsjErmAD5Ya09gkUHNstg8ja6K3O5haTNWSIGGf1ZGi1fY8pZ/VSL32L1BnJsu3d81tnxnpriSWkqSQHG2xcll9L2qxdjxlhPfllL1D9nF1TrCv6QCGzgmnRXs6sgUz7Zb2nZaJzPPnausyktEs56LnQr+dpgGs12G8X4NyqFVo8byNA5/fSwF6WLl7RN4p9fKI1WGZg93yHLG6R1fZ+80N0vebNmRDJCHnTrO2aLOn1dkneCqBExOzj8hJMWljzWGQ==</SignatureValue>
        <KeyInfo>
            <X509Data>
                <X509IssuerSerial>
                    <X509IssuerName>CN=AC UAT,O=SERVICIO DE ADMINISTRACION TRIBUTARIA,OU=SAT-IES Authority,emailAddress=oscar.martinez@sat.gob.mx,street=3ra cerrada de cadiz,postalCode=06370,C=MX,ST=CIUDAD DE MEXICO,L=COYOACAN,x500UniqueIdentifier=2.5.4.45,unstructuredName=responsable: ACDMA-SAT</X509IssuerName>
                    <X509SerialNumber>30001000000400002434</X509SerialNumber>
                </X509IssuerSerial>
                <X509Certificate>MIIFuzCCA6OgAwIBAgIUMzAwMDEwMDAwMDA0MDAwMDI0MzQwDQYJKoZIhvcNAQELBQAwggErMQ8wDQYDVQQDDAZBQyBVQVQxLjAsBgNVBAoMJVNFUlZJQ0lPIERFIEFETUlOSVNUUkFDSU9OIFRSSUJVVEFSSUExGjAYBgNVBAsMEVNBVC1JRVMgQXV0aG9yaXR5MSgwJgYJKoZIhvcNAQkBFhlvc2Nhci5tYXJ0aW5lekBzYXQuZ29iLm14MR0wGwYDVQQJDBQzcmEgY2VycmFkYSBkZSBjYWRpejEOMAwGA1UEEQwFMDYzNzAxCzAJBgNVBAYTAk1YMRkwFwYDVQQIDBBDSVVEQUQgREUgTUVYSUNPMREwDwYDVQQHDAhDT1lPQUNBTjERMA8GA1UELRMIMi41LjQuNDUxJTAjBgkqhkiG9w0BCQITFnJlc3BvbnNhYmxlOiBBQ0RNQS1TQVQwHhcNMTkwNjE3MTk0NDE0WhcNMjMwNjE3MTk0NDE0WjCB4jEnMCUGA1UEAxMeRVNDVUVMQSBLRU1QRVIgVVJHQVRFIFNBIERFIENWMScwJQYDVQQpEx5FU0NVRUxBIEtFTVBFUiBVUkdBVEUgU0EgREUgQ1YxJzAlBgNVBAoTHkVTQ1VFTEEgS0VNUEVSIFVSR0FURSBTQSBERSBDVjElMCMGA1UELRMcRUtVOTAwMzE3M0M5IC8gWElRQjg5MTExNlFFNDEeMBwGA1UEBRMVIC8gWElRQjg5MTExNk1HUk1aUjA1MR4wHAYDVQQLExVFc2N1ZWxhIEtlbXBlciBVcmdhdGUwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQCN0peKpgfOL75iYRv1fqq+oVYsLPVUR/GibYmGKc9InHFy5lYF6OTYjnIIvmkOdRobbGlCUxORX/tLsl8Ya9gm6Yo7hHnODRBIDup3GISFzB/96R9K/MzYQOcscMIoBDARaycnLvy7FlMvO7/rlVnsSARxZRO8Kz8Zkksj2zpeYpjZIya/369+oGqQk1cTRkHo59JvJ4Tfbk/3iIyf4H/Ini9nBe9cYWo0MnKob7DDt/vsdi5tA8mMtA953LapNyCZIDCRQQlUGNgDqY9/8F5mUvVgkcczsIgGdvf9vMQPSf3jjCiKj7j6ucxl1+FwJWmbvgNmiaUR/0q4m2rm78lFAgMBAAGjHTAbMAwGA1UdEwEB/wQCMAAwCwYDVR0PBAQDAgbAMA0GCSqGSIb3DQEBCwUAA4ICAQBcpj1TjT4jiinIujIdAlFzE6kRwYJCnDG08zSp4kSnShjxADGEXH2chehKMV0FY7c4njA5eDGdA/G2OCTPvF5rpeCZP5Dw504RZkYDl2suRz+wa1sNBVpbnBJEK0fQcN3IftBwsgNFdFhUtCyw3lus1SSJbPxjLHS6FcZZ51YSeIfcNXOAuTqdimusaXq15GrSrCOkM6n2jfj2sMJYM2HXaXJ6rGTEgYmhYdwxWtil6RfZB+fGQ/H9I9WLnl4KTZUS6C9+NLHh4FPDhSk19fpS2S/56aqgFoGAkXAYt9Fy5ECaPcULIfJ1DEbsXKyRdCv3JY89+0MNkOdaDnsemS2o5Gl08zI4iYtt3L40gAZ60NPh31kVLnYNsmvfNxYyKp+AeJtDHyW9w7ftM0Hoi+BuRmcAQSKFV3pk8j51la+jrRBrAUv8blbRcQ5BiZUwJzHFEKIwTsRGoRyEx96sNnB03n6GTwjIGz92SmLdNl95r9rkvp+2m4S6q1lPuXaFg7DGBrXWC8iyqeWE2iobdwIIuXPTMVqQb12m1dAkJVRO5NdHnP/MpqOvOgLqoZBNHGyBg4Gqm4sCJHCxA1c8Elfa2RQTCk0tAzllL4vOnI1GHkGJn65xokGsaU4B4D36xh7eWrfj4/pgWHmtoDAYa8wzSwo2GVCZOs+mtEgOQB91/g==</X509Certificate>
            </X509Data>
            <KeyValue>
                <RSAKeyValue>
                    <Modulus>jdKXiqYHzi++YmEb9X6qvqFWLCz1VEfxom2JhinPSJxxcuZWBejk2I5yCL5pDnUaG2xpQlMTkV/7S7JfGGvYJumKO4R5zg0QSA7qdxiEhcwf/ekfSvzM2EDnLHDCKAQwEWsnJy78uxZTLzu/65VZ7EgEcWUTvCs/GZJLI9s6XmKY2SMmv9+vfqBqkJNXE0ZB6OfSbyeE325P94iMn+B/yJ4vZwXvXGFqNDJyqG+ww7f77HYubQPJjLQPedy2qTcgmSAwkUEJVBjYA6mPf/BeZlL1YJHHM7CIBnb3/bzED0n944woio+4+rnMZdfhcCVpm74DZomlEf9KuJtq5u/JRQ==</Modulus>
                    <Exponent>AQAB</Exponent>
                </RSAKeyValue>
            </KeyValue>
        </KeyInfo>
    </Signature>
</Cancelacion>
```

## Objeto de ayuda

**`XmlCancelacionHelper`** te permite usar la librería rápidamente.

Requiere de un objeto `Credentials` que puede ser insertado en la construcción,
puede ser insertado con el método `setCredentials` o por `setNewCredentials`.
La diferencia entre estos dos métodos es que el primero recibe un objeto, y el segundo
recibe los parámetros de ruta al certificado, ruta a la llave privada y contraseña.

En la herramienta de ayuda no se especifica el RFC, cuando se fabrica la solicitud firmada
se obtiene el RFC directamente de las propiedades del certificado.

Los métodos de ayuda utilizan una fecha opcional (`DateTimeImmutable` o `null`), si no se especifica
entonces se toma la fecha actual del sistema, ten en cuenta que para la creación se utiliza el reloj
del sistema y el huso horario. Si no estás seguro de poder controlar estas configuraciones te
recomiendo que establezcas el parámetro.

### Solicitud de cancelación

Para crear la solicitud firmada se puede hacer con los métodos `signCancellation` para un solo UUID
o `signCancellationUuids` para varios UUID. Como primer parámetro reciben qué UUID será cancelado.

### Solicitud de folios relacionados

Para crear la solicitud de folios relacionados se puede hacer con el método `signObtainRelated`.
Requiere el UUID del que se está haciendo la consulta, un rol que define si el RFC desde el que se hace
la consulta se trata de un UUID recibido o emitido y el RFC del PAC por el cual se realiza la consulta.

### Respuesta de aceptación o cancelación a un CFDI

Para crear la solicitud de respuesta usa el método `signCancellationAnswer`.
Requiere el UUID para el cual estás estableciendo la respuesta, la respuesta (aceptación o cancelación)
y el RFC del PAC por el cual se realiza la consulta.

### Solicitud de cancelación de RET

Existe un CFDI especial de *"Retenciones e información de pagos"*, donde también se requiere una solicitud
firmada tal como en una cancelación de CFDI, pero su contenido es diferente.

Para crear la solicitud firmada para RET se puede hacer con los métodos `signRetentionCancellation` para un solo UUID
o `signRetentionCancellationUuids` para varios UUID. Como primer parámetro reciben qué UUID será cancelado.

TIP: Por la experiencia en el uso de los servicios de SAT es recomendado usar siempre cancelaciones individuales.

## Objetos de trabajo

## Documentos a cancelar

**`CancelDocuments`** es una colección de objetos a cancelar. A pesar de que es posible solicitar
la cancelación de múltiples documentos se recomienda enviar uno por uno.

**`CancelDocument`** es la especificación de objeto a cancelar. El objeto se puede crear utilizando el constructor,
o bien, utilizando los métodos de ayuda que incluyen el motivo de la cancelación:

- `CancelDocuments::newWithErrorsRelated(string $uuid, string $substituteOf)`.
- `CancelDocuments::newWithErrorsUnrelated(string $uuid)`.
- `CancelDocuments::newNotExecuted(string $uuid)`.
- `CancelDocuments::newNormativeToGlobal(string $uuid)`.

**`CapsuleInterface`** son los objetos que contienen toda la información relacionada con los datos a firmar,
este tipo de objetos tiene la facultad de poder revisar si el RFC es el mismo usado en la firma así como
poder generar el documento XML a firmar.

**`Credentials`** Es un objeto que encapsula el trabajo con los certificados y llave privada.
Internamente, utiliza [`phpcfdi/credentials`](https://github.com/phpcfdi/credentials) y la clase interna es solo
una indirección de `PhpCfdi\Credentials\Credential`. Incluso puedes crear una credencial de `phpcfd/xml-cancelacion`
a partir de un objeto directo de `phpcfdi/credentials` usando `Credentials::createWithPhpCfdiCredential`, por ejemplo:

```php
<?php
declare(strict_types=1);
use PhpCfdi\Credentials\Credential;
use PhpCfdi\XmlCancelacion\Credentials;
use PhpCfdi\XmlCancelacion\XmlCancelacionHelper;

$phpCfdiCredential = Credential::openFiles('certificado.cer', 'llaveprivada.key', 'contraseña');
$credentials = Credentials::createWithPhpCfdiCredential($phpCfdiCredential);

$xmlCancelacion = new XmlCancelacionHelper($credentials);

$solicitudCancelacion = $xmlCancelacion->signCancellation('11111111-2222-3333-4444-000000000001');
```

**`SignerInterface`** son los objetos que permiten firmar el documento generado por una *cápsula* y una *credencial*.
Existen dos implementaciones: `DOMSigner` (recomendada) y `XmlSecLibsSigner`. La primera no requiere de mayores
dependencias y realiza el firmado utilizando las especificaciones del SAT. La segunda utiliza *parcialmente*
[XmlSecLibs](https://github.com/phpcfdi/xml-cancelacion/blob/main/docs/XmlSecLibs.md) y termina la información de
la firma usando un mecanismo interno.

## Observaciones

Al parecer es obligatorio incluir en la firma los nombres de espacio `xmlns:xsd` y `xmlns:xsi` aunque no se ocupen.
Si bien, esto no es necesario para producir un documento con la firma correcta, sí parece ser necesario para
producir la información que se requiere por parte del PAC o del SAT.

A partir de 2019-08-27 con la versión `1.0.0` se puede usar [`robrichards/xmlseclibs`](https://github.com/robrichards/xmlseclibs).
Para más información ver el archivo [XmlSecLibs](https://github.com/phpcfdi/xml-cancelacion/blob/main/docs/XmlSecLibs.md).

A partir de 2019-08-13 con la versión `0.4.0` se eliminó la dependencia a `eclipxe/cfdiutils` y se cambió a la
librería [`phpcfdi/credentials`](https://github.com/phpcfdi/xml-cancelacion), con esta nueva dependencia se trabaja
mucho mejor con los certificados y llaves privadas.

## Compatibilidad

Esta librería se mantendrá compatible con al menos la versión con
[soporte activo de PHP](https://www.php.net/supported-versions.php) más reciente.

También utilizamos [Versionado Semántico 2.0.0](docs/SEMVER.md) por lo que puedes
usar esta librería sin temor a romper tu aplicación.

## Contribuciones

Las contribuciones con bienvenidas. Por favor, revisa [CONTRIBUTING][] para más detalles
y recuerda revisar el archivo de tareas pendientes [TODO][] y el archivo [CHANGELOG][].

## Copyright and License

The `phpcfdi/xml-cancelacion` library is copyright © [PhpCfdi](https://www.phpcfdi.com/)
and licensed for use under the MIT License (MIT). Please see [LICENSE][] for more information.

[contributing]: https://github.com/phpcfdi/xml-cancelacion/blob/main/CONTRIBUTING.md
[changelog]: https://github.com/phpcfdi/xml-cancelacion/blob/main/docs/CHANGELOG.md
[todo]: https://github.com/phpcfdi/xml-cancelacion/blob/main/docs/TODO.md

[source]: https://github.com/phpcfdi/xml-cancelacion
[php-version]: https://packagist.org/packages/phpcfdi/xml-cancelacion
[discord]: https://discord.gg/aFGYXvX
[release]: https://github.com/phpcfdi/xml-cancelacion/releases
[license]: https://github.com/phpcfdi/xml-cancelacion/blob/main/LICENSE
[build]: https://github.com/phpcfdi/xml-cancelacion/actions/workflows/build.yml?query=branch:main
[reliability]:https://sonarcloud.io/component_measures?id=phpcfdi_xml-cancelacion&metric=Reliability
[maintainability]: https://sonarcloud.io/component_measures?id=phpcfdi_xml-cancelacion&metric=Maintainability
[coverage]: https://sonarcloud.io/component_measures?id=phpcfdi_xml-cancelacion&metric=Coverage
[violations]: https://sonarcloud.io/project/issues?id=phpcfdi_xml-cancelacion&resolved=false
[downloads]: https://packagist.org/packages/phpcfdi/xml-cancelacion

[badge-source]: https://img.shields.io/badge/source-phpcfdi/xml--cancelacion-blue?logo=github
[badge-discord]: https://img.shields.io/discord/459860554090283019?logo=discord
[badge-php-version]: https://img.shields.io/packagist/php-v/phpcfdi/xml-cancelacion?logo=php
[badge-release]: https://img.shields.io/github/release/phpcfdi/xml-cancelacion?logo=git
[badge-license]: https://img.shields.io/github/license/phpcfdi/xml-cancelacion?logo=open-source-initiative
[badge-build]: https://img.shields.io/github/actions/workflow/status/phpcfdi/xml-cancelacion/build.yml?branch=main&logo=github-actions
[badge-reliability]: https://sonarcloud.io/api/project_badges/measure?project=phpcfdi_xml-cancelacion&metric=reliability_rating
[badge-maintainability]: https://sonarcloud.io/api/project_badges/measure?project=phpcfdi_xml-cancelacion&metric=sqale_rating
[badge-coverage]: https://img.shields.io/sonar/coverage/phpcfdi_xml-cancelacion/main?logo=sonarcloud&server=https%3A%2F%2Fsonarcloud.io
[badge-violations]: https://img.shields.io/sonar/violations/phpcfdi_xml-cancelacion/main?format=long&logo=sonarcloud&server=https%3A%2F%2Fsonarcloud.io
[badge-downloads]: https://img.shields.io/packagist/dt/phpcfdi/xml-cancelacion?logo=packagist
