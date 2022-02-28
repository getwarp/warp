# Changelog

All notable changes to `type` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [3.0.0] - Not Released Yet

### Changed

- Minimal supported PHP version bumped up to 7.4.
- Use typehints and static analysis by PHPStan as much as possible.
- Rename `DisjunctionType` to `UnionType` and all related classes.
- Rename `ConjunctionType` to `IntersectionType` and all related classes.
- Rename `CompositeTypeFactory` to `TypeFactoryAggregate`.
- New static constructor style which allow type objects to be lazy singletons.
- `UnionType` and `IntersectionType` constructors now accepts variadic arguments.
- Introduce separate type casters which replace old `BuiltinType::cast()`.

## [2.5.1] - 2021-06-13

### Added

- `Interface` suffix added to type interface.

## [2.5.0] - 2021-04-21

### Added

-   First release from monorepo.

## [1.3.1] - 2021-02-16

### Added

-   Support installation on PHP 8

## [1.3.0] - 2020-10-17

### Added

-   Lot of improvements for `DisjunctionType` and `ConjunctionType`:
    -   General code moved to `AbstractAggregatedType` class;
    -   They can be iterated over now;
    -   They can be merged with other aggregated types using late static binding;
    -   Duplicates auto removed from given subtypes;
    -   At least 2 subtypes required.
-   `DisjunctionTypeFactory` and `ConjunctionTypeFactory` also improved:
    -   General code moved to `AbstractAggregatedTypeFactory` class;
    -   They can now parse complex type strings, such as: `int|string|array<bool|int>|string|null`.
-   `GroupTypeFactory` allows parsing grouped with `()` type strings like: `(string|int)[]`
-   `MemoizedTypeFactory` allows cache results of calls to `supports()` and `make()`. Recommend wrap type factory with
    memoized decorator in production.

### Fixed

-   Fixed string representation of collection type with complex value type.

## [1.2.0] - 2020-10-13

### Removed

-   `Type` interface doesn't declare static methods `support()` and `create()` no more.

### Added

-   Dynamic type factories. It replaces static methods in `Type` classes and static `TypeFactory` class.
-   Mixed type.
-   Void type.

### Deprecated

-   Static methods in classes implementing `Type` interface and static `TypeFactory` class marked as deprecated. Their API
    still backward compatible using dynamic type factories. It will be removed in next major release.

## [1.1.0] - 2020-10-04

### Added

-   Support non strict mode for all scalar types (int, float, string and bool)
-   Force return `null` when casting to null builtin type

## [1.0.1] - 2020-09-26

### Fixed

-   Development config updates
-   Minor type issues fixes found by static analyser

## [1.0.0] - 2020-06-01

-   First release
