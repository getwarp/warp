# Changelog

All notable changes to `getwarp/laminas-hydrator-bridge` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [3.1.0] - 2022-08-22

Bump up version.

## [3.0.0] - 2022-04-22

### Changed

- Minimal supported PHP version bumped up to 7.4.
- Use typehints and static analysis by PHPStan as much as possible.
- Namespace changed: `Warp\LaminasHydratorBridge` -> `Warp\Bridge\LaminasHydrator`.

### Added

- `SafeReflectionHydrator`: acts like default reflection hydrator, but skips extraction of not initialized properties.
- `BlameStrategy`: strategy to hydrate/extract blame object from `data-source` package.
- `CasterStrategy`: strategy to hydrate value using caster from `type` package.
- `DateValueStrategy`: strategy to hydrate/extract datetime object from `value-object` package.
- `ValueObjectStrategy`: strategy to hydrate/extract value object from `value-object` package.
- `JsonStrategy`: strategy to hydrate/extract data to json format.

### Removed

- `ScalarStrategy`: can be replaced by `CasterStrategy` with `ScalarCaster`.

## [2.5.4] - 2022-06-12

### Misc

- Replaces `spaceonfire/laminas-hydrator-bridge` on packagist.
- Adds autoloader polyfill.

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

## [1.0.0] - 2020-10-05

-   First release.
