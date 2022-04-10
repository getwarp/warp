<div align="center">

# getwarp/laminas-hydrator-bridge

Integration for [Laminas Hydrator][link-laminas-hydrator] with other Warp components

[GitHub][link-github] •
[Packagist][link-packagist] •
[Installation](#installation) •
[Usage](#usage)

</div>

## Installation

Via Composer

```bash
$ composer require getwarp/laminas-hydrator-bridge
```

## Usage

```php
use Warp\LaminasHydratorBridge\StdClassHydrator;
use Warp\LaminasHydratorBridge\NamingStrategy\AliasNamingStrategy;
use Warp\LaminasHydratorBridge\Strategy\BooleanStrategy;
use Warp\LaminasHydratorBridge\Strategy\ScalarStrategy;
use Warp\LaminasHydratorBridge\Strategy\NullableStrategy;
use Warp\Type\BuiltinType;

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

[Report issues][link-issues] and [send Pull Requests][link-pulls] in the [main warp repository][link-monorepo].
Please see [CONTRIBUTING][link-contributing] and [CODE_OF_CONDUCT][link-code-of-conduct] for details.

## Credits

- [Constantine Karnaukhov][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [license file](LICENSE.md) for more information.

[link-github]: https://github.com/getwarp/laminas-hydrator-bridge
[link-packagist]: https://packagist.org/packages/getwarp/laminas-hydrator-bridge
[link-laminas-hydrator]: https://github.com/laminas/laminas-hydrator
[link-author]: https://github.com/hustlahusky
[link-contributors]: ../../contributors
[link-monorepo]: https://github.com/getwarp/warp
[link-issues]: https://github.com/getwarp/warp/issues
[link-pulls]: https://github.com/getwarp/warp/pulls
[link-contributing]: https://github.com/getwarp/warp/blob/3.0.x/CONTRIBUTING.md
[link-code-of-conduct]: https://github.com/getwarp/.github/blob/main/CODE_OF_CONDUCT.md
