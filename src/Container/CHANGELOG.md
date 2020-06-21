# Changelog

All notable changes to `container` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

<!--
## [X.Y.Z] - YYYY-MM-DD
### Added
- Nothing

### Deprecated
- Nothing

### Fixed
- Nothing

### Removed
- Nothing

### Security
- Nothing
-->

## [2.0.1] - 2020-06-21
### Fixed
- Resolve definition in `Container` class using parent container

## [2.0.0] - 2020-06-21
### Added
- `ArgumentResolver` class as default implementation of `ResolverInterface`
- `ReflectionFactory` class creates instance of any existing class and resolve constructor arguments
- `ReflectionInvoker` class calls any given callable with resolved arguments
- `ReflectionContainer` class acts like factory for any existing class

### Removed
- `Container` class does not manage any existing class no more
- `Container` class does not implements `ResolverInterface` no more
- Removed `AbstractContainerDecorator` class

## [1.0.0] - 2020-06-11
### Added
- First release
