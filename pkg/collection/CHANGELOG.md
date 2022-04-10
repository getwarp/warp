# Changelog

All notable changes to `getwarp/collection` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [3.0.0] - Not Released Yet

### Changed

- Minimal supported PHP version bumped up to 7.4.
- Use typehints and static analysis by PHPStan as much as possible.
- New Collection API: with generics, lazy operations based on generators and mutation methods available.
- Introduce Map API: when you need to operate with key-value data.
- New static constructor style.

## [2.5.1] - 2021-06-13

### Added

- Changes caused by minor updates in `spaceonfire/type` library.

## [2.5.0] - 2021-04-21

### Added

-   First release from monorepo.

## [2.1.1] - 2021-02-16

### Added

-   Support installation on PHP 8.

## [2.1.0] - 2020-10-13

### Added

-   Update `spaceonfire/type` library up to `^1.2`. Replace deprecated static `TypeFactory` call in `TypedCollection`.

## [2.0.1] - 2020-09-26

### Fixed

-   Development config updates
-   Minor type issues fixes found by static analyser

## [2.0.0] - 2020-06-04

### Added

-   Make collection interface stricter
-   Added abstract decorator
-   Added `IndexedCollection` decorator
-   `TypedCollection` now acts as decorator

### Deprecated

-   `TypedCollection` now final. Extend it with a decorator.

### Removed

-   `BaseCollection` has been removed. Use decorators if you need to add new functionality to collections.

## [1.5.0] - 2020-06-01

### Added

-   Use `spaceonfire/type` for checking typed collection items type

### Fixed

-   Fix division by zero when calling average() on empty collection

## [1.4.0] - 2020-05-31

### Added

-   Update `CollectionInterface`:
    -   update method signatures according to `BaseCollection`
    -   update phpDoc comments
-   Replace Closure with callable type
-   Move collection aliases to trait
-   Implement FilterableInterface from `spaceonfire/criteria` by collection

### Fixed

-   Fixed `ArrayHelper::unflatten()` method.

## [1.3.0] - 2020-03-07

### Added

-   New methods added:
    -   `CollectionInterface::unique()`
    -   `CollectionInterface::implode()`
    -   `CollectionInterface::first()`
    -   `CollectionInterface::last()`
    -   `CollectionInterface::firstKey()`
    -   `CollectionInterface::lastKey()`
    -   `CollectionInterface::average()`
    -   `CollectionInterface::median()`
-   Method aliases added:
    -   `BaseCollection::avg()` alias to `BaseCollection::average()`
    -   `BaseCollection::join()` alias to `BaseCollection::implode()`

## [1.2.2] - 2020-03-07

### Fixed

-   Declare `CollectionInterface::merge` attributes
-   Fix example in README.md

## [1.2.1] - 2019-11-09

### Fixed

-   Fix `BaseCollection::filter` call with empty callback

## [1.2.0] - 2019-11-08

### Added

-   Huge update for `TypedCollection`:
    -   check type on item add to collection
    -   add `downgrade` method that returns simple `Collection` instance
    -   override some methods witch logic requires downgrade, restore original `TypedCollection` when we can
    -   cover `BaseCollection` and `TypedCollection` with tests

## [1.1.1] - 2019-10-18

### Fixed

-   Fix checking class and interface existence in TypedCollection

## [1.1.0] - 2019-10-18

### Added

-   Add TypedCollection
-   Documentation generated

## [1.0.0] - 2019-10-03

### Added

-   Base collection implementation
