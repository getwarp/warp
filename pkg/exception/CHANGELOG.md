# Changelog

All notable changes to `warp/exception` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [3.0.0] - 2022-04-22

### Added

- Minimal supported PHP version bumped up to 7.4.
- Use typehints and static analysis by PHPStan as much as possible.
- Default trait implementation for `yiisoft/friendly-exception` contract.
- Extend `yiisoft/friendly-exception` contract with client friendly exception to mark exception that can be safely shown
  to end user.
- Translatable exceptions with `symfony/translation-contracts`.
