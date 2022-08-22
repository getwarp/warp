# Changelog

All notable changes to `getwarp/common` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [3.1.0] - 2022-08-22

Bump up version.

## [3.0.0] - 2022-04-22

### Changed

- Minimal supported PHP version bumped up to 7.4.
- Use typehints and static analysis by PHPStan as much as possible.
- Support for symfony v6 components.
- Move `ArrayHelper` from collection package (without methods available in `yiisoft/arrays`).

### Added

- Lazy singletons.
- Interface to mark static constructor method `::new()`.
- Fields API: allows extracting data from object/array using `symfony/property-access`, `yiisoft/arrays` or implement
  your own.

## [2.5.4] - 2022-06-12

### Misc

- Replaces `spaceonfire/common` on packagist.
- Adds autoloader and functions polyfills.

## [2.5.3] - 2022-04-22

Release under a `getwarp` vendor name.

### Changed

- Replace first level namespace to `Warp\`.

## [2.5.0] - 2021-04-21

### Added

- First release from monorepo.

## [1.0.1] - 2021-02-19

### Added

-   Support installation on PHP 8.

## [1.0.0] - 2020-12-30

Initial release.
