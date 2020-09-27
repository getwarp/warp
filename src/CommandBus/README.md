# command-bus

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]
[![Code Coverage][ico-coverage]][link-actions]
[![Build Status][ico-build-status]][link-actions]

This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what
PSRs you support to avoid any confusion with users and contributors.

## Structure

If any of the following are applicable to your project, then the directory structure should follow industry best practices by being named the following.

```
bin/
build/
docs/
config/
src/
tests/
vendor/
```

## Install

Via Composer

```bash
$ composer require spaceonfire/command-bus
```

## Usage

```php
use spaceonfire\CommandBus\CommandBus;
use spaceonfire\CommandBus\Mapping\MapByStaticList;

class MyCommand
{
}

class MyCommandHandler
{
    public function handle(MyCommand $command)
    {
        // Do your job to handle a command
    }
}

$commandBus = new CommandBus(new MapByStaticList([
    MyCommand::class => [MyCommandHandler::class, 'handle'],
]));

$commandBus->handle(new MyCommand());
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

```bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email genteelknight@gmail.com instead of using the issue tracker.

## Credits

- [Constantine Karnaukhov][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/spaceonfire/command-bus.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/spaceonfire/command-bus.svg?style=flat-square
[ico-coverage]: https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fgist.githubusercontent.com%2Fhustlahusky%2Fd62607c1a2e4707959b0142e0ea876cd%2Fraw%2F0674734de845e5d449f969efe52cbe9e6bc1e77a%2Fspaceonfire-command-bus.json
[ico-build-status]: https://github.com/spaceonfire/command-bus/workflows/Build%20Pipeline/badge.svg
[link-packagist]: https://packagist.org/packages/spaceonfire/command-bus
[link-downloads]: https://packagist.org/packages/spaceonfire/command-bus
[link-author]: https://github.com/hustlahusky
[link-contributors]: ../../contributors
[link-actions]: ../../actions
