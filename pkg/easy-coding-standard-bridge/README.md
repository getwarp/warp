<div align="center">

# getwarp/easy-coding-standard-bridge

Warp config for [Easy Coding Standard][link-ecs]

[GitHub][link-github] •
[Packagist][link-packagist] •
[Installation](#installation) •
[Usage](#usage)

</div>

## Installation

Установите зависимости для разработки:

```bash
composer require --dev getwarp/easy-coding-standard-bridge symplify/easy-coding-standard symfony/var-exporter
```

## Usage

Чтобы скопировать базовые конфиги для Easy Coding Standard и EditorConfig, выполните команду из директории репозитория.

```bash
vendor/bin/ecs-init.php
```

После этого у вас появятся файлы `.editorconfig` и `ecs.php`. Доработайте конфиг `ecs.php` под нужды проекта, укажите
директории с кодом для анализа.

## Внедрение на действующий проект

После инициализации конфига на действующем проекте `ecs` найдет массу ошибок в кодовой базе, не все из которых он может
решить автоматически при использовании ключа `--fix`. В этом случае можно просто принять все эти ошибки как "baseline"
и игнорировать их при анализе. Это позволяет упростить процесс внедрения инструмента и начать писать новый код применяя
стандарты.

Для создания "baseline" конфига необходимо сначала собрать ошибки в формате JSON:

```bash
vendor/bin/ecs check --output-format=json > ecs-baseline-errors.json
```

После этого запустить команду для генерации "baseline" конфига на основе полученного JSON файла:

```bash
vendor/bin/ecs-baseliner.php ecs-baseline-errors.json
```

Эта команда создаст файл `ecs-baseline.php` в рабочей директории. Его необходимо подключить в файле `ecs.php`
(по-умолчанию он уже подключен в шаблоне). После чего можно проверить что ошибки игнорируются.

Если после этого `ecs` снова найдет ошибки, повторите сбор ошибок в новый файл (например, `ecs-baseline-errors.2.json`)
и повторите генерацию "baseline" конфига с указанием всех файлов ошибок. Повторите эти шаги, пока `ecs` не перестанет
выводить ошибки.

```bash
vendor/bin/ecs-baseliner.php ecs-baseline-errors.json ecs-baseline-errors.2.json
```

После внедрения "baseline" конфига не забывайте при редактировании legacy кода убирать файлы из "baseline" конфига и
исправлять ошибки, тем самым постепенно приводя кодовую базу проекта в соответствие стандартам.

## Запуск в CI/CD

Пример задания для запуска проверки кода в GitLab CI/CD:

```yml
stages:
    - test

.in-docker-job:
    tags:
        - docker
    image: alpine

.php-job:
    extends: .in-docker-job
    image: getwarp/nginx-php-fpm:latest-7.4

.composer-job:
    extends: .php-job
    before_script:
        - composer install
    cache:
        key: composer
        paths:
            - vendor
            - $COMPOSER_CACHE_DIR
    variables:
        COMPOSER_CACHE_DIR: "$CI_PROJECT_DIR/._composer-cache"

codestyle:
    extends: .composer-job
    stage: test
    script:
        - composer codestyle -- --no-progress-bar --no-interaction
```

При указании docker образа, в котором будет выполняться задание, вместо `getwarp/nginx-php-fpm:latest-7.4` можно
указать тег с версией php для проекта (доступны `7.2`, `7.3`, `7.4`) либо другой образ с `php` и `composer`.

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

[link-github]: https://github.com/getwarp/easy-coding-standard-bridge
[link-packagist]: https://packagist.org/packages/getwarp/easy-coding-standard-bridge
[link-author]: https://github.com/hustlahusky
[link-contributors]: ../../contributors
[link-monorepo]: https://github.com/getwarp/warp
[link-issues]: https://github.com/getwarp/warp/issues
[link-pulls]: https://github.com/getwarp/warp/pulls
[link-contributing]: https://github.com/getwarp/warp/blob/3.0.x/CONTRIBUTING.md
[link-code-of-conduct]: https://github.com/getwarp/.github/blob/main/CODE_OF_CONDUCT.md
[link-ecs]: https://github.com/symplify/easy-coding-standard
