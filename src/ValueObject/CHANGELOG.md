# Changelog

All notable changes to `spaceonfire/value-object` will be documented in this file.

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

## [1.4.0] - 2020-08-09
### Added
- Added optional `$nullValuePredicate` argument to `NullableStrategy` constructor
  that allows you to specify which values should be considered as `null`

## [1.3.1] - 2020-06-18
### Fixed
- Make value object strategy more flexible

## [1.3.0] - 2020-06-15
### Added
- Added `BooleanStrategy`

## [1.2.0] - 2020-06-12
### Added
- Added `NullableStrategy`

## [1.1.0] - 2020-04-21
### Added
- Added phpDoc comments as possible
- Updated code style checks
- Added PHPStan linter
- Make `UuidValue` non abstract class

## [1.0.2] - 2020-04-13
### Fixed
- Support `ramsey/uuid@^4.0`

## [1.0.1] - 2020-03-27
### Fixed
- Support input value validation and casting for all value objects

## [1.0.0] - 2020-03-25
### Added
- Value objects for scalars (`IntValue`, `StringValue`)
- Value objects for special formats (`EmaiilValue`, `IpValue`, `UriValue`, `UuidValue`)
- Enums value objects
- DateTime value objects
