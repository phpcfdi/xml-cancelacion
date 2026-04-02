# phpcfdi/xml-cancelacion To Do List

## Pendientes

- El tercer parámetro del constructor de `Cancellation` que recibe el valor `DocumentType` debería ser obligatorio,
  es opcional para compatibilidad con la versión actual.

- Mejorar los casos de cobertura de código para hacer mandatorio `infection` en los pasos de construcción.

- La librería `robrichards/xmlseclibs` a la fecha 2026-04-01 no es compatible con PHP 8.5.
  Se debe remover el operador de ignorar errores en el archivo `SignerImplementationTestCase`
  al llamar a la función estática `XMLSecEnc::staticLocateKeyInfo()`.

## Resueltas

- Generar excepciones internas en lugar de excepciones genéricas de SPL.
- Poner el copyright correcto en cuanto esté el sitio de PhpCfdi
- Dejar de usar CfdiUtils y usar phpcfdi/credentials cuando esté publicada y estable
