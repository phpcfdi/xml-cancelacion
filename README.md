# phpcfdi/xml-cancelacion

[![Source Code][badge-source]][source]
[![Latest Version][badge-release]][release]
[![Software License][badge-license]][license]
[![Build Status][badge-build]][build]
[![Scrutinizer][badge-quality]][quality]
[![Coverage Status][badge-coverage]][coverage]
[![Total Downloads][badge-downloads]][downloads]

> Genera documentos de cancelación de CFDI firmados (XMLSEC)

:us: The documentation of this project is in spanish as this is the natural language for intented audience.

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

```php
<?php
use PhpCfdi\XmlCancelacion\Capsule;
use PhpCfdi\XmlCancelacion\CapsuleSigner;
use PhpCfdi\XmlCancelacion\Credentials;

// certificado, llave privada y clave de llave
$credentials = new Credentials('certificado.cer.pem', 'privatekey.key.pem', '12345678a');

// datos de cancelación
$data = new Capsule('LAN7008173R5', ['12345678-1234-1234-1234-123456789012']);

// generación del xml
$xml = (new CapsuleSigner())->sign($data, $credentials);
```

La salida esperada es algo como lo siguiente (sin los espacios en blanco que agregué para mejor lectura).

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


## Objetos principales

**`Capsule`** es un contenedor de información que contiene RFC, Fecha y UUID.

**`Credentials`** contiene la *ruta a los archivos* de certificado y llave privada en *formato PEM*,
así como la clave para usar la llave privada.

**`CapsuleSigner`** genera el XML usando un `Capsule` y un `Credentials`.


## Observaciones

Al parecer es obligatorio incluir en la firma los nombres de espacio `xmlns:xsd` y `xmlns:xsi` aunque no se ocupen.
Si bien, esto no es necesario para producir un documento con la firma correcta, sí parece ser necesario para
producir la información que se requiere por parte del PAC o del SAT.

Se podría utilizar [`robrichards/xmlseclibs`] para hacer el firmado, sin embargo al 2019-04-09 aun no se
habían implementado los mecanismos para incluir el `RSAKeyValue`, a pesar de tener un
[PR abierto](https://github.com/robrichards/xmlseclibs/pull/75) desde 2015-09-03.
Las otras dos desventajas están en la forma en que escribe los valores de `X509IssuerSerial`.
Que aunque creo que no son muy relevantes para generar una firma correcta, sí podrían ser importantes,
y motivo de rechazo -o pretexto- en el servicio de cancelación del SAT.

Al momento existe una dependencia fuerte a `eclipxe/cfdiutils`, sin embargo, esta dependencia va a desaparecer porque
se va a crear un nuevo paquete bajo la organización `PhpCfdi` para certificados, llaves privadas y llaves públicas.


## Compatilibilidad

Esta librería se mantendrá compatible con al menos la versión con
[soporte activo de PHP](http://php.net/supported-versions.php) más reciente.

También utilizamos [Versionado Semántico 2.0.0](https://semver.org/lang/es/) por lo que puedes usar esta librería
sin temor a romper tu aplicación.


## Contribuciones

Las contribuciones con bienvenidas. Por favor lee [CONTRIBUTING][] para más detalles
y recuerda revisar el archivo de tareas pendientes [TODO][] y el [CHANGELOG][].


## Copyright and License

The phpcfdi/xml-cancelacion library is copyright © [Carlos C Soto](http://eclipxe.com.mx/)
and licensed for use under the MIT License (MIT). Please see [LICENSE][] for more information.


[contributing]: https://github.com/phpcfdi/xml-cancelacion/blob/master/CONTRIBUTING.md
[changelog]: https://github.com/phpcfdi/xml-cancelacion/blob/master/docs/CHANGELOG.md
[todo]: https://github.com/phpcfdi/xml-cancelacion/blob/master/docs/TODO.md

[source]: https://github.com/phpcfdi/xml-cancelacion
[release]: https://github.com/phpcfdi/xml-cancelacion/releases
[license]: https://github.com/phpcfdi/xml-cancelacion/blob/master/LICENSE
[build]: https://travis-ci.org/phpcfdi/xml-cancelacion?branch=master
[quality]: https://scrutinizer-ci.com/g/phpcfdi/xml-cancelacion/
[coverage]: https://scrutinizer-ci.com/g/phpcfdi/xml-cancelacion/code-structure/master/code-coverage
[downloads]: https://packagist.org/packages/phpcfdi/xml-cancelacion

[badge-source]: http://img.shields.io/badge/source-phpcfdi/xml--cancelacion-blue.svg?style=flat-square
[badge-release]: https://img.shields.io/github/release/phpcfdi/xml-cancelacion.svg?style=flat-square
[badge-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[badge-build]: https://img.shields.io/travis/phpcfdi/xml-cancelacion/master.svg?style=flat-square
[badge-quality]: https://img.shields.io/scrutinizer/g/phpcfdi/xml-cancelacion/master.svg?style=flat-square
[badge-coverage]: https://img.shields.io/scrutinizer/coverage/g/phpcfdi/xml-cancelacion/master.svg?style=flat-square
[badge-downloads]: https://img.shields.io/packagist/dt/phpcfdi/xml-cancelacion.svg?style=flat-square
