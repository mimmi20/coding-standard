# CONTRIBUTING

We are using [GitHub Actions](https://github.com/features/actions) as a continuous integration system.

For details, take a look at the following workflow configuration files:

- [`workflows/continuous-integration.yaml`](workflows/continuous-integration.yaml)
- [`workflows/lock-closed-issues.yaml`](workflows/lock-closed-issues.yaml)
- [`workflows/stale.yaml`](workflows/stale.yaml)

## Coding Standards

We are using [`ergebnis/composer-normalize`](https://github.com/ergebnis/composer-normalize) to normalize `composer.json`.

We are using [`friendsofphp/php-cs-fixer`](https://github.com/FriendsOfPHP/PHP-CS-Fixer) and [`squizlabs/php_codesniffer`](https://github.com/squizlabs/PHP_CodeSniffer) to enforce coding standards in PHP files.

Run

```sh
vendor/bin/phpcs
vendor/bin/php-cs-fixer fix --dry-run
```
