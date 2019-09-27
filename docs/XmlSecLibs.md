# Uso de XmlSecLibs `robrichards/xmlseclibs`

A partir de la versión `0.5.0` se incluye un objeto `XmlSecLibsSigner` que implementa `SignerInterface`.

Se puede utilizar [`robrichards/xmlseclibs`](https://github.com/robrichards/xmlseclibs) para hacer el firmado,
sin embargo al 2019-04-09 aun no se han implementado los mecanismos para incluir el elemento `KeyValue`,
a pesar de tener un [PR #75](https://github.com/robrichards/xmlseclibs/pull/75) desde 2015-09-03.

Las otras dos desventajas están en la forma en que escribe los valores de `X509IssuerSerial`,
tanto `X509IssuerName` como `X509SerialNumber`.

Por lo anterior, para escribir el contenido de `KeyValue` usando `XmlSecLibsSigner` se usa la misma
implementación manual y no la incompleta/incorrecta de XmlSecLibs.

## Ejemplo de uso:

```php
<?php declare(strict_types=1);

use PhpCfdi\XmlCancelacion\Signers\XmlSecLibsSigner;
use PhpCfdi\XmlCancelacion\XmlCancelacionHelper;

$xmlhelper = new XmlCancelacionHelper();
$xmlhelper->setSigner(new XmlSecLibsSigner()); // change signer to XmlSecLibsSigner
$cancellation = $xmlhelper->signCancellation('11111111-2222-3333-4444-000000000001');
```


## Instalación

Recuerda que `robrichards/xmlseclibs` no es una dependencia (es una recomendación) de `phpcfdi/xml-cancelacion`

```shell script
# instalar esta librería
composer require phpcfdi/xml-cancelacion

# instalar xmlseclibs
composer require robrichards/xmlseclibs
```
