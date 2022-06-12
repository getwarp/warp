# Changelog

All notable changes to `getwarp/container` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [2.5.4] - 2022-06-12

### Misc

- Replaces `spaceonfire/container` on packagist.
- Adds autoloader polyfill.

## [2.5.3] - 2022-04-22

Release under a `getwarp` vendor name.

### Changed

- Replace first level namespace to `Warp\`.

## [2.5.1] - 2021-06-13

### Fixed

-   Code style fixes.

## [2.5.0] - 2021-04-21

### Added

-   First release from monorepo.

## [2.4.1] - 2021-02-19

### Added

-   Support installation on PHP 8.

## [2.4.0] - 2020-12-19

### Added

-   `Warp\Container\RawValueHolder` class added and can be used for definition of concrete value.
-   `null` used as default value for parameters that allows it.

### Deprecated

-   Class `Warp\Container\Argument\ArgumentValue` replaced with `Warp\Container\RawValueHolder`. Class alias
    provided for backwards compatibility, but will be removed in next major release.
-   `ContainerAwareInterface::setContainer()` should not be considered to return `$this`. It will be void in next major
    release.

### Fixed

-   Reflection factory now does not try to instantiate abstract classes.
    `Warp\Container\Exception\CannotInstantiateAbstractClassException` threw instead.
-   Argument resolves with default value for abstract classes when available.

## [2.3.0] - 2020-10-24

### Added

-   Added priority option for containers in `Warp\Container\CompositeContainer`.

## [2.2.0] - 2020-10-22

### Deprecated

-   Class `Warp\Container\ContainerChain` renamed to `Warp\Container\CompositeContainer`. This name clearly
    describes what this class does and just fits best. Class alias provided for backwards compatibility, but will be
    removed in next major release.

## [2.1.1] - 2020-09-26

### Fixed

-   Development config updates
-   Fix PhpDoc comment in service provider aggregate

## [2.1.0] - 2020-09-23

### Added

-   Support definition tags

## [2.0.1] - 2020-06-21

### Fixed

-   Resolve definition in `Container` class using parent container

## [2.0.0] - 2020-06-21

### Added

-   `ArgumentResolver` class as default implementation of `ResolverInterface`
-   `ReflectionFactory` class creates instance of any existing class and resolve constructor arguments
-   `ReflectionInvoker` class calls any given callable with resolved arguments
-   `ReflectionContainer` class acts like factory for any existing class

### Removed

-   `Container` class does not manage any existing class no more
-   `Container` class does not implements `ResolverInterface` no more
-   Removed `AbstractContainerDecorator` class

## [1.0.0] - 2020-06-11

### Added

-   First release
