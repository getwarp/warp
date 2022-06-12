# Changelog

All notable changes to `getwarp/value-object` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [2.5.4] - 2022-06-12

### Misc

- Replaces `spaceonfire/value-object` on packagist.
- Adds autoloader polyfill.

## [2.5.3] - 2022-04-22

Release under a `getwarp` vendor name.

### Changed

- Replace first level namespace to `Warp\`.

## [2.5.1] - 2021-06-13

### Fixed

-   Update date value strategy for more flexible hydration.

## [2.5.0] - 2021-04-21

### Added

-   First release from monorepo.

## [1.7.1] - 2021-02-19

### Added

-   Support installation on PHP 8.

## [1.7.0] - 2020-12-20

### Added

-   Method `equals()` now available on any value object with strict value comparison.

### Deprecated

-   `IntValue::equalsTo()` method deprecated and should be replaced with new `equals()` method.

### Fixed

-   Enum value object now accept only public constants ([#5](https://github.com/getwarp/value-object/issues/5)).
-   Enum value objects now strictly compared due to new method `equals()`.

## [1.6.0] - 2020-10-06

### Deprecated

-   Classes `Warp\ValueObject\Bridge\LaminasHydrator\BooleanStrategy`
    and `Warp\ValueObject\Bridge\LaminasHydrator\NullableStrategy`
    were moved to `getwarp/laminas-hydrator-bridge` library. Class alias provided for backwards compatibility, but
    will be removed in next major release.

## [1.5.0] - 2020-09-27

### Deprecated

-   Namespace `Warp\ValueObject\Integrations\HydratorStrategy` renamed
    to `Warp\ValueObject\Bridge\LaminasHydrator`. Class aliases provided for backwards compatibility, but will be
    removed in next major release.

## [1.4.0] - 2020-08-09

### Added

-   Added optional `$nullValuePredicate` argument to `NullableStrategy` constructor that allows you to specify which
    values should be considered as `null`

## [1.3.1] - 2020-06-18

### Fixed

-   Make value object strategy more flexible

## [1.3.0] - 2020-06-15

### Added

-   Added `BooleanStrategy`

## [1.2.0] - 2020-06-12

### Added

-   Added `NullableStrategy`

## [1.1.0] - 2020-04-21

### Added

-   Added phpDoc comments as possible
-   Updated code style checks
-   Added PHPStan linter
-   Make `UuidValue` non abstract class

## [1.0.2] - 2020-04-13

### Fixed

-   Support `ramsey/uuid@^4.0`

## [1.0.1] - 2020-03-27

### Fixed

-   Support input value validation and casting for all value objects

## [1.0.0] - 2020-03-25

### Added

-   Value objects for scalars (`IntValue`, `StringValue`)
-   Value objects for special formats (`EmaiilValue`, `IpValue`, `UriValue`, `UuidValue`)
-   Enums value objects
-   DateTime value objects
