# Changelog

All notable changes to `getwarp/data-source` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [2.5.4] - 2022-06-12

### Misc

- Replaces `spaceonfire/data-source` on packagist.
- Adds autoloader polyfill.

## [2.5.3] - 2022-04-22

Release under a `getwarp` vendor name.

### Changed

- Replace first level namespace to `Warp\`.

## [2.5.0] - 2021-04-21

### Added

-   First release from monorepo.

## [1.4.1] - 2021-02-25

### Fixed

-   Refactor CycleQuery.

## [1.4.0] - 2021-02-25

### Added

-   Fix CycleQuery::count() after query gets paginated with matching()
-   Update processing PaginableCriteria in CycleQuery

## [1.3.1] - 2021-02-19

### Added

-   Support installation on PHP 8.

## [1.3.0] - 2020-12-20

### Added

-   New simplified repository adapter for Cycle ORM introduced to separate schema builder from a repository functionality.
-   Entity interface simplified and now does not extend `ArrayAccess` and `JsonSerializable` (but `AbstractEntity` still
    does it).

### Deprecated

-   `AbstractCycleRepository` class is deprecated and should be replaced with new `AbstractCycleRepositoryAdapter` class.

## [1.2.0] - 2020-10-06

### Deprecated

-   Class `Warp\DataSource\Bridge\CycleOrm\Mapper\Hydrator\StdClassHydrator` moved to
    `getwarp/laminas-hydrator-bridge` library. Class alias provided for backwards compatibility, but will be removed
    in next major release.

## [1.1.0] - 2020-09-27

### Deprecated

-   Namespace `Warp\DataSource\Adapters` renamed to `Warp\DataSource\Bridge`. Class aliases provided for
    backwards compatibility, but will be removed in next major release.

## [1.0.0] - 2020-06-12

### Added

-   First release
