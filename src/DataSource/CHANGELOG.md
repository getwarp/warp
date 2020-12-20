# Changelog

All notable changes to `spaceonfire/data-source` will be documented in this file.

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

## [1.3.0] - 2020-12-20

### Added

- New simplified repository adapter for Cycle ORM introduced to separate schema builder from a repository functionality.
- Entity interface simplified and now does not extend `ArrayAccess` and `JsonSerializable` (but `AbstractEntity` still
  does it).

### Deprecated

- `AbstractCycleRepository` class is deprecated and should be replaced with new `AbstractCycleRepositoryAdapter` class.

## [1.2.0] - 2020-10-06

### Deprecated

- Class `spaceonfire\DataSource\Bridge\CycleOrm\Mapper\Hydrator\StdClassHydrator` moved to
  `spaceonfire/laminas-hydrator-bridge` library. Class alias provided for backwards compatibility, but will be removed
  in next major release.

## [1.1.0] - 2020-09-27

### Deprecated

- Namespace `spaceonfire\DataSource\Adapters` renamed to `spaceonfire\DataSource\Bridge`. Class aliases provided for
  backwards compatibility, but will be removed in next major release.

## [1.0.0] - 2020-06-12

### Added

- First release
