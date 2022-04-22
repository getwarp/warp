# Changelog

All notable changes to `warp/command-bus` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [2.5.3] - 2022-04-22

Release under a `getwarp` vendor name.

### Changed

- Replace first level namespace to `Warp\`.

## [2.5.1] - 2021-06-13

### Added

-   `Interface` suffix added to all interfaces that missing it.

## [2.5.0] - 2021-04-21

### Added

-   First release from monorepo.

## [1.2.2] - 2021-02-19

### Added

-   Support installation on PHP 8.

## [1.2.1] - 2020-10-01

### Fixed

-   Check container has handler class before asking for it.

## [1.2.0] - 2020-09-27

### Added

-   Added logger middleware uses [PSR-3 logger](https://github.com/php-fig/log) implementation.
-   Added profiler middleware uses [symfony's stopwatch component](https://github.com/symfony/stopwatch).

## [1.1.0] - 2020-05-10

### Added

-   Added more flexible ways to declare command to handler mapping.
-   Split `CommandToHandlerMapping` interface into `ClassNameMappingInterface` and `MethodNameMappingInterface`.
-   Added next method name mappings: `StaticMethodNameMapping`.
-   Added next class name mappings: `ReplacementClassNameMapping`, `SuffixClassNameMapping`, `ClassNameMappingChain`.
-   Added `CompositeMapping`.

## [1.0.2] - 2020-04-06

### Fixed

-   Cloning command bus: Rebind nested middleware callbacks for cloned command bus.

## [1.0.1] - 2020-04-06

### Fixed

-   Rebind middleware chain on command bus cloning.

## [1.0.0] - 2020-04-01

### Added

-   Simple command bus.
