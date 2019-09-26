# Actualización de 0.4 a 0.5

:us: This document is in spanish as this is the primary language of the target developers

## Cambios rápidos

Si estás usando el objeto de ayuda `XmlCancelacionHelper` disponible desde la versión `0.4`:

- El método `make` se cambia a `signCancellation`.
- El método `makeUuids` se cambia a `signCancellationUuids`.

Si estás usando `Capsule` directamente:

```text
<?php
$credentials = new Credentials('certificado.cer.pem', 'privatekey.key.pem', '12345678a');

// cambia este código
$data = new Capsule('LAN7008173R5', ['12345678-1234-1234-1234-123456789012']);
$xml = (new CapsuleSigner())->sign($data, $credentials);

// a este código
$xmlCancelacion = new XmlCancelacionHelper($credentials);
$xml = $xmlCancelacion->signCancellation(['12345678-1234-1234-1234-123456789012']);

// o a este otro
$data = new CancellationCapsule('LAN7008173R5', ['12345678-1234-1234-1234-123456789012']);
$signer = new DOMSigner(); // o new XmlSecLibsSigner()
$xml = $signer->signCapsule($data, $credentials);
```

## Razones del cambio de la librería

El SAT ofrece algunos servicios a través de los PAC como una pasarela, al menos conozco tres servicios documentados:
solicitud de cancelación, solicitud de UUID relacionados y aceptación/rechazo de cancelaciones pendientes.

La solicitud de cancelación es lo único que se cubría hasta la versión `0.4.x` y en la versión `0.5.x` se han
creado las firmas de las otras dos comunicaciones.

Este cambio evidenció que se necesitaba restructurar la librería para poder firmar los mensajes de
solicitud de UUID relacionados y aceptación/rechazo de cancelaciones pendientes porque antes solo se procesaba el
mensaje de solicitud de cancelación.

## Nuevos conceptos desde la versión 0.5.0

A los mensajes se les llaman *cápsulas (capsule)* y tienen tres características: Almacenan los datos del mensaje,
pueden producir un documento XML `DOMDocument` con la información que será firmada y pueden verificar que el RFC
de la cápsula coincide con un RFC (usado para verificar que se está usando la el CSD correcto)

Para que una cápsula fabrique el documento XML utiliza un *DocumentBuilder*.
A cada cápsula le corresponde un objeto relacionado para fabricar este documento.

Los documentos XML pueden ser firmados utilizando cualquier objeto que implemente `SignerInterface`.
Anteriormente solo se producían utilizando `DOMSigner`, ahora `DOMSigner` es el usado por defecto pero se puede cambiar.

También se provee un objeto `XmlSecLibsSigner` que puede producir (parcialmente) la firma, lo que no puede hacer es
agregar el elemento `KeyInfo` tal como lo quiere el SAT, por lo que para esta última tarea se usa la forma interna. 

## Mejor uso de credenciales

El objeto `PhpCfdi\XmlCancelacion\Credentials` internamente utiliza un objeto `PhpCfdi\Credentials\Credential`.
En la versión `0.5` se introduce un método constructor `createWithPhpCfdiCredential()` para poder fabricar el objeto
`Credentials` usando un objeto `Credential` ya existente.

Esto es útil porque de esta manera el certificado y llave privada no necesitan existir en el sistema de archivos local
y se puede crear la credencial de esta librería con los datos de una credencial de PhpCfdi.

```php
<?php

use PhpCfdi\Credentials\Certificate;
use PhpCfdi\Credentials\Credential;
use PhpCfdi\Credentials\PrivateKey;

$certificateContents = ''; // contenido del certificado desde algún lugar, como la base de datos
$privateKeyPemContents = ''; // contenido de la llave privada como PEM desde algún lugar, como la base de datos
$passPhrase = '';
$phpCfdiCretential = new Credential(
    new Certificate($certificateContents),
    new PrivateKey($privateKeyPemContents, $passPhrase)
);

$credentials = PhpCfdi\XmlCancelacion\Credentials::createWithPhpCfdiCredential($phpCfdiCretential);
```

## Excepciones de dominio

- `interface XmlCancelacionException`
    - `class XmlCancelacionLogicException` extends `\LogicException`
        - `class DocumentWithoutRootElement`
        - `class HelperDoesNotHaveCredentials`
        - `class InvalidCapsuleType`
    - `class XmlCancelacionRuntimeException` extends `\RuntimeException`
        - `class CannotLoadCertificateAndPrivateKey`
        - `class CapsuleRfcDoesnotBelongToCertificateRfc`
        - `class CertificateIsNotCSD`

Todas las excepciones producidas por esta librería son de tipo `XmlCancelacionException`.

Pueden ser únicamente de dos tipos: `XmlCancelacionLogicException` y `XmlCancelacionRuntimeException`.

Las excepciones lógicas son guardas de implementaciones erróneas, no se espera que se produzcan
y si ocurren debe ser una implementación errónea de la librería.

Las excepciones de tiempo de ejecución ocurren por una verificación realizada que no permite
continuar con la tarea, se espera que quien implementa la librería atrape estas excepciones
para informar de la situación no esperada.

## BC Changes with compatibility layer

```text
roave-backward-compatibility-check roave-backwards-compatibility-check:assert-backwards-compatible --from e7f83c21886353f451dcc873bfb0a652e9503f58

[BC] REMOVED: Method PhpCfdi\XmlCancelacion\XmlCancelacionHelper#createCapsule() was removed
[BC] REMOVED: Method PhpCfdi\XmlCancelacion\XmlCancelacionHelper#createCapsuleSigner() was removed
[BC] REMOVED: Method PhpCfdi\XmlCancelacion\DOMSigner#createKeyValueElement() was removed
[BC] CHANGED: The parameter $document of PhpCfdi\XmlCancelacion\DOMSigner#__construct() changed from DOMDocument to ?DOMDocument
[BC] CHANGED: The number of required arguments for PhpCfdi\XmlCancelacion\DOMSigner#createKeyInfoElement() increased from 4 to 5
[BC] CHANGED: The parameter $issuerName of PhpCfdi\XmlCancelacion\DOMSigner#createKeyInfoElement() changed from string to a non-contravariant DOMDocument
[BC] CHANGED: The parameter $pubKeyData of PhpCfdi\XmlCancelacion\DOMSigner#createKeyInfoElement() changed from array to a non-contravariant string
[BC] CHANGED: The parameter $issuerName of PhpCfdi\XmlCancelacion\DOMSigner#createKeyInfoElement() changed from string to DOMDocument
[BC] CHANGED: The parameter $pubKeyData of PhpCfdi\XmlCancelacion\DOMSigner#createKeyInfoElement() changed from array to string
[BC] CHANGED: Method createKeyValueElement() of class PhpCfdi\XmlCancelacion\DOMSigner visibility reduced from protected to private
[BC] CHANGED: The number of required arguments for PhpCfdi\XmlCancelacion\DOMSigner#createKeyValueElement() increased from 1 to 2
[BC] CHANGED: The parameter $pubKeyData of PhpCfdi\XmlCancelacion\DOMSigner#createKeyValueElement() changed from array to a non-contravariant DOMDocument
[BC] CHANGED: The parameter $pubKeyData of PhpCfdi\XmlCancelacion\DOMSigner#createKeyValueElement() changed from array to DOMDocument
[BC] CHANGED: Method defaultExtraNamespaces() of class PhpCfdi\XmlCancelacion\CapsuleSigner changed scope from instance to static
```

Los cambios expuestos se tratan de métodos protegidos o que no deben afectar el uso normal de la aplicación
