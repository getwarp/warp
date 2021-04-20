# Laminas Hydrator Bridge by spaceonfire

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-packagist]
![Code Coverage][ico-coverage]

Provides integration for [Laminas Hydrator][link-laminas-hydrator] with some [`spaceonfire`][link-packagist-vendor]
libraries.

## Install

Via Composer

```bash
$ composer require spaceonfire/laminas-hydrator-bridge
```

## Usage

```php
use spaceonfire\LaminasHydratorBridge\StdClassHydrator;
use spaceonfire\LaminasHydratorBridge\NamingStrategy\AliasNamingStrategy;
use spaceonfire\LaminasHydratorBridge\Strategy\BooleanStrategy;
use spaceonfire\LaminasHydratorBridge\Strategy\ScalarStrategy;
use spaceonfire\LaminasHydratorBridge\Strategy\NullableStrategy;
use spaceonfire\Type\BuiltinType;

$hydrator = new StdClassHydrator();

$hydrator->setNamingStrategy(new AliasNamingStrategy([
    'firstName' => ['first_name', 'firstname'],
    'lastName' => ['last_name', 'lastname'],
    'rulesAccepted' => ['rules_accepted'],
]));

$hydrator->addStrategy('age', new NullableStrategy(new ScalarStrategy(BuiltinType::INT)));
$hydrator->addStrategy('rulesAccepted', new BooleanStrategy(['Y', 'y', 1], 'N', false));

$john = $hydrator->hydrate([
    'first_name' => 'John',
    'last_name' => 'Doe',
    'rules_accepted' => 'y',
    'age' => '25',
], new stdClass());

// $john->firstName === 'John';
// $john->lastName === 'Doe';
// $john->rulesAccepted === true;
// $john->age === 25;

$jane = $hydrator->hydrate([
    'firstname' => 'Jane',
    'lastname' => 'Doe',
    'rules_accepted' => '',
    'age' => null,
], new stdClass());

// $jane->firstName === 'John';
// $jane->lastName === 'Doe';
// $jane->rulesAccepted === false;
// $jane->age === null;
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

[Report issues][link-issues] and [send Pull Requests][link-pulls] in the [main spaceonfire repository][link-monorepo].
Please see [CONTRIBUTING][link-contributing] and [CODE_OF_CONDUCT][link-code-of-conduct] for details.

## Credits

-   [Constantine Karnaukhov][link-author]
-   [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/spaceonfire/laminas-hydrator-bridge.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/spaceonfire/laminas-hydrator-bridge.svg?style=flat-square
[ico-coverage]: https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fgist.githubusercontent.com%2Fhustlahusky%2Fd62607c1a2e4707959b0142e0ea876cd%2Fraw%2Flaminas-hydrator-bridge.json
[link-packagist]: https://packagist.org/packages/spaceonfire/laminas-hydrator-bridge
[link-packagist-vendor]: https://packagist.org/packages/spaceonfire
[link-laminas-hydrator]: https://github.com/laminas/laminas-hydrator
[link-author]: https://github.com/hustlahusky
[link-contributors]: ../../contributors
[link-monorepo]: https://github.com/spaceonfire/spaceonfire
[link-issues]: https://github.com/spaceonfire/spaceonfire/issues
[link-pulls]: https://github.com/spaceonfire/spaceonfire/pulls
[link-contributing]: https://github.com/spaceonfire/spaceonfire/blob/master/CONTRIBUTING.md
[link-code-of-conduct]: https://github.com/spaceonfire/spaceonfire/blob/master/CODE_OF_CONDUCT.md
