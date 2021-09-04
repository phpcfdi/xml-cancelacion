# phpcfdi/xml-cancelacion To Do List

## Pendientes

- El tercer parámetro del constructor de `Cancellation` que recibe el valor `DocumentType` debería ser obligatorio,
  es opcional para compatibilidad con la versión actual.

- Mejorar los casos de cobertura de código para hacer mandatorio `infection` en los pasos de construcción.

## Resueltas

- Generar excepciones internas en lugar de excepciones genéricas de SPL.
- Poner el copyright correcto en cuanto esté el sitio de PhpCfdi
- Dejar de usar CfdiUtils y usar phpcfdi/credentials cuando esté publicada y estable
