# Contributing

Contributions are **welcome** and will be fully **credited**.

We accept contributions via Pull Requests on [Github](https://github.com/spaceonfire/spaceonfire).

## Pull Requests

- **Fork** this repository.
- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.
- **Create feature branches** - Don't ask us to pull from your master branch.
- Tests, code style and static analysis checks **must pass**.
- **Add tests!** - Your patch won't be accepted if it doesn't have tests.

## Getting started

Clone your fork repository:

```bash
git clone git@github.com:<your-github-username>/spaceonfire.git
cd spaceonfire
```

We recommend to use Docker for your dev environment.

```bash
cp .env.example .env
docker-compose build
docker-compose up -d
```

This will start docker container with minimal supported by our packages version of PHP (7.2). You can select different
version of base image by running this commands:

```bash
# cp docker-compose.<php-version>.yml docker-compose.override.yml
cp docker-compose.8.0.yml docker-compose.override.yml
docker-compose build
docker-compose up -d
```

You can also modify `docker-compose.override.yml` file like you want due it ignored by git.

To enter docker container use this command:

```bash
docker-compose exec app su-exec nginx bash
```

Now you can install composer dependencies, run tests and static analysis, etc.

## Scripts

**Check code style**. We are using [PSR-12 Coding Standard][psr12].

```bash
composer codestyle
```

**Fix code style**

```bash
composer codestyle -- --fix
```

**Running static analysis**

```bash
composer lint
```

**Running tests**

```bash
composer test
```

[psr12]: https://www.php-fig.org/psr/psr-12/
