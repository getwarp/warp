<div align="center">

# getwarp/value-object

Value Object library for PHP

[GitHub][link-github] •
[Packagist][link-packagist] •
[Installation](#installation) •
[Usage](#usage)

</div>

## Installation

Via Composer

```bash
$ composer require getwarp/value-object
```

## Usage

```php
use Warp\ValueObject\AbstractIntValue;
use Warp\ValueObject\AbstractEnumValue;

class PostId extends AbstractIntValue {
}

$postId = PostId::new(10);
\assert($int->value() === 10);
\assert(PostId::new(10) === $postId);

/**
 * @method static self public()
 * @method static self draft()
 */
class PostStatus extends AbstractEnumValue {
    public const PUBLIC = 'public';

    public const DRAFT = 'draft';
}

$draftStatus = PostStatus::draft();
$publicStatus = PostStatus::public();

\assert($draftStatus !== $publicStatus);
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

[Report issues][link-issues] and [send pull requests][link-pulls] in the [main Warp repository][link-monorepo]. Please
see [contributing guide][link-contributing] and [code of conduct][link-code-of-conduct] for details.

## Credits

- [Constantine Karnaukhov][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [license file](LICENSE.md) for more information.

[link-github]: https://github.com/getwarp/value-object
[link-packagist]: https://packagist.org/packages/getwarp/value-object
[link-author]: https://github.com/hustlahusky
[link-contributors]: ../../contributors
[link-monorepo]: https://github.com/getwarp/warp
[link-issues]: https://github.com/getwarp/warp/issues
[link-pulls]: https://github.com/getwarp/warp/pulls
[link-contributing]: https://github.com/getwarp/warp/blob/3.1.x/CONTRIBUTING.md
[link-code-of-conduct]: https://github.com/getwarp/.github/blob/main/CODE_OF_CONDUCT.md
