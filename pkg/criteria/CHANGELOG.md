# Changelog

All notable changes to `criteria` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [3.0.0] - Not Released Yet

### Changed

- Minimal supported PHP version bumped up to 7.4.
- Use typehints and static analysis by PHPStan as much as possible.
- New static constructor style.
- Immutable criteria design.
- Expression factory is lazy singleton now.
- Selector expression use fields API.

### Removed

- Criteria decorators.
- Doctrine expression converter.
- `JsonApiCriteriaBuilder` class.

## [2.5.1] - 2021-06-13

### Fixed

- Code style fixes.

## [2.5.0] - 2021-04-21

### Added

-   First release from monorepo.

## [1.2.0] - 2021-02-25

### Added

-   Fix some errors with PaginableCriteria.

## [1.1.1] - 2021-02-16

### Added

-   Support installation on PHP 8.

## [1.1.0] - 2020-09-27

### Deprecated

-   Namespace `spaceonfire\Criteria\Adapter` renamed to `spaceonfire\Criteria\Bridge`. Class aliases provided for
    backwards compatibility, but will be removed in next major release.

## [1.0.0] - 2020-05-27

### Added

-   First stable release.
