# phpcfdi/xml-cancelacion

[![Source Code][badge-source]][source]
[![Latest Version][badge-release]][release]
[![Software License][badge-license]][license]
[![Build Status][badge-build]][build]
[![Scrutinizer][badge-quality]][quality]
[![Coverage Status][badge-coverage]][coverage]
[![Total Downloads][badge-downloads]][downloads]

[![Reliability][badge-sonar-reliability]][sonar-reliability]
[![Maintainability][badge-sonar-maintainability]][sonar-maintainability]
[![Code Coverage][badge-sonar-coverage]][sonar-coverage]
[![Violations][badge-sonar-violations]][sonar-violations]

> Genera documentos de cancelación de CFDI firmados (XMLSEC)

:us: The documentation of this project is in spanish as this is the natural language for intended audience.

:mexico: La documentación del proyecto está en español porque ese es el lenguaje principal de los usuarios.

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
use PhpCfdi\XmlCancelacion\XmlCancelacionHelper;
use PhpCfdi\XmlCancelacion\Definitions\RfcRole;
use PhpCfdi\XmlCancelacion\Definitions\CancelAnswer;

$xmlCancelacion = new XmlCancelacionHelper();

$solicitudCancelacion = $xmlCancelacion
    ->setNewCredentials('certificado.cer', 'llaveprivada.key', 'contraseña')
    ->signCancellation('11111111-2222-3333-4444-000000000001');

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
use PhpCfdi\XmlCancelacion\Signers\DOMSigner;
use PhpCfdi\XmlCancelacion\Credentials;

// certificado, llave privada y clave de llave
$credentials = new Credentials('certificado.cer.pem', 'privatekey.key.pem', '12345678a');

// datos de cancelación
$data = new Cancellation('LAN7008173R5', ['12345678-1234-1234-1234-123456789012'], new DateTimeImmutable());

// generación del xml
$xml = (new DOMSigner())->signCapsule($data, $credentials);
```

La salida esperada es algo como lo siguiente (sin los espacios en blanco, que agregué para mejor lectura).

```xml
<?xml version="1.0" encoding="UTF-8"?>
<Cancelacion xmlns="http://cancelacfd.sat.gob.mx"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    RfcEmisor="LAN7008173R5" Fecha="2019-04-05T16:29:17">
  <Folios>
    <UUID>12345678-1234-1234-1234-123456789012</UUID>
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
        <DigestValue>j2x4spEq57R1mQD9lwXh2mmOyK8=</DigestValue>
      </Reference>
    </SignedInfo>
    <SignatureValue>e0Cyi/rXOTFwW8ckNnwQEQ1oC6m73PDvExunnniCsZWQrDRV2SiaH9NoAhJhb5W9p5vJgB+PWu4J6uchG7EikDPbDPw19K3B7uZKTH7tZLffV/bZx6rozzreInvP+S1HhrnOqLPwebBm3Q3yRQk3pbaW2sHFPPuRPLqP+1h3Fegv4GEnwy+0G7LRg3H05v6fDXvONgikCrC2sdzA0kM6qvrOpGfbgBd4au7eFFRjCA4oX9zcQUG9E4m+uVovj0ebp4EqDn9SC+Az3fi5AHom6adju8wx4uJvi8isVg8ZP9KcuqEfXhIkyFutJrD61l00+XyZe4n5T1Aya+Ta0Q6NrA==</SignatureValue>
    <KeyInfo>
      <X509Data>
        <X509IssuerSerial>
          <X509IssuerName>/CN=CINDEMEX SA DE CV/name=CINDEMEX SA DE CV/O=CINDEMEX SA DE CV/x500UniqueIdentifier=LAN7008173R5 / FUAB770117BXA/serialNumber= / FUAB770117MDFRNN09/OU=Prueba_CFDI</X509IssuerName>
          <X509SerialNumber>20001000000300022815</X509SerialNumber>
        </X509IssuerSerial>
        <X509Certificate>MIIFxTCCA62gAwIBAgIUMjAwMDEwMDAwMDAzMDAwMjI4MTUwDQYJKoZIhvcNAQELBQAwggFmMSAwHgYDVQQDDBdBLkMuIDIgZGUgcHJ1ZWJhcyg0MDk2KTEvMC0GA1UECgwmU2VydmljaW8gZGUgQWRtaW5pc3RyYWNpw7NuIFRyaWJ1dGFyaWExODA2BgNVBAsML0FkbWluaXN0cmFjacOzbiBkZSBTZWd1cmlkYWQgZGUgbGEgSW5mb3JtYWNpw7NuMSkwJwYJKoZIhvcNAQkBFhphc2lzbmV0QHBydWViYXMuc2F0LmdvYi5teDEmMCQGA1UECQwdQXYuIEhpZGFsZ28gNzcsIENvbC4gR3VlcnJlcm8xDjAMBgNVBBEMBTA2MzAwMQswCQYDVQQGEwJNWDEZMBcGA1UECAwQRGlzdHJpdG8gRmVkZXJhbDESMBAGA1UEBwwJQ295b2Fjw6FuMRUwEwYDVQQtEwxTQVQ5NzA3MDFOTjMxITAfBgkqhkiG9w0BCQIMElJlc3BvbnNhYmxlOiBBQ0RNQTAeFw0xNjEwMjUyMTUyMTFaFw0yMDEwMjUyMTUyMTFaMIGxMRowGAYDVQQDExFDSU5ERU1FWCBTQSBERSBDVjEaMBgGA1UEKRMRQ0lOREVNRVggU0EgREUgQ1YxGjAYBgNVBAoTEUNJTkRFTUVYIFNBIERFIENWMSUwIwYDVQQtExxMQU43MDA4MTczUjUgLyBGVUFCNzcwMTE3QlhBMR4wHAYDVQQFExUgLyBGVUFCNzcwMTE3TURGUk5OMDkxFDASBgNVBAsUC1BydWViYV9DRkRJMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAgvvCiCFDFVaYX7xdVRhp/38ULWto/LKDSZy1yrXKpaqFXqERJWF78YHKf3N5GBoXgzwFPuDX+5kvY5wtYNxx/Owu2shNZqFFh6EKsysQMeP5rz6kE1gFYenaPEUP9zj+h0bL3xR5aqoTsqGF24mKBLoiaK44pXBzGzgsxZishVJVM6XbzNJVonEUNbI25DhgWAd86f2aU3BmOH2K1RZx41dtTT56UsszJls4tPFODr/caWuZEuUvLp1M3nj7Dyu88mhD2f+1fA/g7kzcU/1tcpFXF/rIy93APvkU72jwvkrnprzs+SnG81+/F16ahuGsb2EZ88dKHwqxEkwzhMyTbQIDAQABox0wGzAMBgNVHRMBAf8EAjAAMAsGA1UdDwQEAwIGwDANBgkqhkiG9w0BAQsFAAOCAgEAJ/xkL8I+fpilZP+9aO8n93+20XxVomLJjeSL+Ng2ErL2GgatpLuN5JknFBkZAhxVIgMaTS23zzk1RLtRaYvH83lBH5E+M+kEjFGp14Fne1iV2Pm3vL4jeLmzHgY1Kf5HmeVrrp4PU7WQg16VpyHaJ/eonPNiEBUjcyQ1iFfkzJmnSJvDGtfQK2TiEolDJApYv0OWdm4is9Bsfi9j6lI9/T6MNZ+/LM2L/t72Vau4r7m94JDEzaO3A0wHAtQ97fjBfBiO5M8AEISAV7eZidIl3iaJJHkQbBYiiW2gikreUZKPUX0HmlnIqqQcBJhWKRu6Nqk6aZBTETLLpGrvF9OArV1JSsbdw/ZH+P88RAt5em5/gjwwtFlNHyiKG5w+UFpaZOK3gZP0su0sa6dlPeQ9EL4JlFkGqQCgSQ+NOsXqaOavgoP5VLykLwuGnwIUnuhBTVeDbzpgrg9LuF5dYp/zs+Y9ScJqe5VMAagLSYTShNtN8luV7LvxF9pgWwZdcM7lUwqJmUddCiZqdngg3vzTactMToG16gZA4CWnMgbU4E+r541+FNMpgAZNvs2CiW/eApfaaQojsZEAHDsDv4L5n3M1CC7fYjE/d61aSng1LaO6T1mh+dEfPvLzp7zyzz+UgWMhi5Cs4pcXx1eic5r7uxPoBwcCTt3YI1jKVVnV7/w=</X509Certificate>
      </X509Data>
      <KeyValue>
        <RSAKeyValue>
          <Modulus>gvvCiCFDFVaYX7xdVRhp/38ULWto/LKDSZy1yrXKpaqFXqERJWF78YHKf3N5GBoXgzwFPuDX+5kvY5wtYNxx/Owu2shNZqFFh6EKsysQMeP5rz6kE1gFYenaPEUP9zj+h0bL3xR5aqoTsqGF24mKBLoiaK44pXBzGzgsxZishVJVM6XbzNJVonEUNbI25DhgWAd86f2aU3BmOH2K1RZx41dtTT56UsszJls4tPFODr/caWuZEuUvLp1M3nj7Dyu88mhD2f+1fA/g7kzcU/1tcpFXF/rIy93APvkU72jwvkrnprzs+SnG81+/F16ahuGsb2EZ88dKHwqxEkwzhMyTbQ==</Modulus>
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

**`CapsuleInterface`** son los objetos que contienen toda la información relacionada con los datos a firmar,
este tipo de objetos tiene la facultad de poder revisar si el RFC es el mismo usado en la firma así como
poder generar el documento XML a firmar.

**`Credentials`** Es un objeto que encapsula el trabajo con los certificados y llave privada.
Internamente utiliza [`phpcfdi/credentials`](https://github.com/phpcfdi/credentials) y la clase interna es solo
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

Las contribuciones con bienvenidas. Por favor lee [CONTRIBUTING][] para más detalles
y recuerda revisar el archivo de tareas pendientes [TODO][] y el archivo [CHANGELOG][].

## Copyright and License

The `phpcfdi/xml-cancelacion` library is copyright © [PhpCfdi](https://www.phpcfdi.com/)
and licensed for use under the MIT License (MIT). Please see [LICENSE][] for more information.

[contributing]: https://github.com/phpcfdi/xml-cancelacion/blob/main/CONTRIBUTING.md
[changelog]: https://github.com/phpcfdi/xml-cancelacion/blob/main/docs/CHANGELOG.md
[todo]: https://github.com/phpcfdi/xml-cancelacion/blob/main/docs/TODO.md

[source]: https://github.com/phpcfdi/xml-cancelacion
[release]: https://github.com/phpcfdi/xml-cancelacion/releases
[license]: https://github.com/phpcfdi/xml-cancelacion/blob/main/LICENSE
[build]: https://github.com/phpcfdi/xml-cancelacion/actions/workflows/build.yml?query=branch:main
[quality]: https://scrutinizer-ci.com/g/phpcfdi/xml-cancelacion/
[coverage]: https://scrutinizer-ci.com/g/phpcfdi/xml-cancelacion/code-structure/main/code-coverage
[downloads]: https://packagist.org/packages/phpcfdi/xml-cancelacion

[sonar-reliability]:https://sonarcloud.io/component_measures?id=phpcfdi_xml-cancelacion&metric=Reliability
[sonar-maintainability]: https://sonarcloud.io/component_measures?id=phpcfdi_xml-cancelacion&metric=Maintainability
[sonar-coverage]: https://sonarcloud.io/component_measures?id=phpcfdi_xml-cancelacion&metric=Coverage
[sonar-violations]: https://sonarcloud.io/project/issues?id=phpcfdi_xml-cancelacion&resolved=false
[badge-source]: https://img.shields.io/badge/source-phpcfdi/xml--cancelacion-blue?style=flat-square
[badge-release]: https://img.shields.io/github/release/phpcfdi/xml-cancelacion?style=flat-square
[badge-license]: https://img.shields.io/github/license/phpcfdi/xml-cancelacion?style=flat-square
[badge-build]: https://img.shields.io/github/workflow/status/phpcfdi/xml-cancelacion/build/main?style=flat-square
[badge-quality]: https://img.shields.io/scrutinizer/g/phpcfdi/xml-cancelacion/main?style=flat-square
[badge-coverage]: https://img.shields.io/scrutinizer/coverage/g/phpcfdi/xml-cancelacion/main?style=flat-square
[badge-downloads]: https://img.shields.io/packagist/dt/phpcfdi/xml-cancelacion?style=flat-square

[badge-sonar-reliability]: https://sonarcloud.io/api/project_badges/measure?project=phpcfdi_xml-cancelacion&metric=reliability_rating
[badge-sonar-maintainability]: https://sonarcloud.io/api/project_badges/measure?project=phpcfdi_xml-cancelacion&metric=sqale_rating
[badge-sonar-coverage]: https://img.shields.io/sonar/coverage/phpcfdi_xml-cancelacion/main?logo=sonarcloud&server=https%3A%2F%2Fsonarcloud.io
[badge-sonar-violations]: https://img.shields.io/sonar/violations/phpcfdi_xml-cancelacion/main?format=long&logo=sonarcloud&server=https%3A%2F%2Fsonarcloud.io