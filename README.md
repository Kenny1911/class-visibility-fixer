# ClassVisibilityFixer

\[ English | [Русский](./README-RU.md) \]

`ClassVisibilityFixer` is an extension for [PHP-CS-Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer) that
automatically adds the `@api`, `@internal` and/or `@psalm-internal` annotation to classes.

## Installation

```bash
composer require --dev kenny1911/class-visibility-fixer
```

## Configure

Add `ClassVisibilityFixer` to your PHP-CS-Fixer configuration:

```php
<?php
$finder = \PhpCsFixer\Finder::create()
    ->in(__DIR__);

return (new \PhpCsFixer\Config())
    ->registerCustomFixers([
        new \Kenny1911\ClassVisibilityFixer\ClassVisibilityFixer(),
    ])
    ->setRules([
        'Kenny1911/class_visibility' => true,
    ])
    ->setFinder($finder);
```

## Usage

Then run PHP-CS-Fixer:

```bash
vendor/bin/php-cs-fixer fix
```

## Settings

- `defaultVisibility` — defines which annotation to add by default. Possible values:
  - `internal+psalm-internal` (default value) — adds `@internal` and `@psalm-internal` annotations (including the
    namespace).
  - `internal` — adds only `@internal`
  - `psalm-internal` — adds only `@psalm-internal` (including the namespace of the current class)
  - `api` — adds `@api`

## Example

Before:

```php
namespace App\Service;

class ExampleClass
{
    // code
}
```

After applying the fixer with the `defaultVisibility: api` setting:

```php
namespace App\Service;

/**
 * @api
 */
class ExampleClass
{
    // code
}
```

After applying the fixer with the `defaultVisibility: internal+psalm-internal` setting:

```php
namespace App\Service;

/**
 * @internal
 * @psalm-internal App\Service
 */
class ExampleClass
{
    // code
}
```

## License

This project is licensed under the MIT License.

## Author

Developed by [Kenny1911](https://github.com/Kenny1911).

## Similar Projects

- [typhoon/check-visibility-psalm-plugin](https://github.com/typhoon-php/check-visibility-psalm-plugin) - A plugin for
  Psalm with a similar purpose. It can be used together with `ClassVisibilityFixer`.