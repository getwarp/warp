# Changelog

All notable changes to `warp/cycle-bridge` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [3.1.0] - 2022-08-XX

### Changed

- Provide service factory to migrator's file repository

## [3.0.2] - 2022-06-12

### Misc

- Use `cycle/database` instead of `spiral/database`

## [3.0.0] - 2022-04-22

### Added

- Minimal supported PHP version bumped up to 7.4.
- Use typehints and static analysis by PHPStan as much as possible.
- Split Cycle ORM integration from `data-source` package.
- Update API according `data-source` package changes.
- Custom mappers with plugins support: domain events, blame (timestamps), entity reference, fields grouping, belongs-to
  autolink.
- Custom to-many relations with lazy collections support.
- Entity reference wrapper.
- Integration with `criteria` package: convert expressions to query builder filters, support filter by relation using
  entity object.
- CLI commands for cycle/migrations with lock support.
- Describe ORM schema with arrays.
