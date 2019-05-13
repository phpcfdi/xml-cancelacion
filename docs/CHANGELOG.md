# CHANGELOG

## Version 0.2.0 2019-05-13

- Code quality improvements thanks to phpstan and infection (mutation testing framework)
- Remove `Capsule::append` method, `Capsule` is now immutable.
- Throw `LogicException` when `DOMSigner` uses a document that does not have root element
- `Capsule` now implements `Countable`


## Version 0.1.0 2019-04-09

- First public version
