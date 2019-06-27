# phpcfdi/xml-cancelacion To Do List

- Poner el copyright correcto en cuanto esté el sitio de PhpCfdi
- Dejar de usar CfdiUtils y usar phpcfdi/credentials cuando esté publicada y estable

## Breaking changes for next major release

- Remove _protected methods_ `DOMSigner::createKeyValue` and `DOMSigner::createKeyValueFromCertificado`.
- Rename `DOMSigner::createKeyValueFromPemContents` to `DOMSigner::createKeyValue`.
