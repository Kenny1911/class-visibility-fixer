# ClassVisibilityFixer

\[ [English](./README.md) | Russian \]

`ClassVisibilityFixer` — это дополнение для [PHP-CS-Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer), которое
автоматически добавляет аннотацию `@api`, `@internal` и/или `@psalm-internal` к классам.

## Установка

```bash
composer require --dev kenny1911/class-visibility-fixer
```

## Конфигурация

Добавьте `ClassVisibilityFixer` в конфигурацию PHP-CS-Fixer:

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

## Использвоание

После этого запустите PHP-CS-Fixer:

```bash
vendor/bin/php-cs-fixer fix
```

## Настройки

- `defaultVisibility` — определяет, какую аннотацию добавлять по умолчанию. Возможные значения:
  - `internal+psalm-internal` (значение по умолчанию) — добавляет аннотации `@internal` и `@psalm-internal` (включая
    namespace).
  - `internal` — добавляет толко `@internal`
  - `psalm-internal` — добавляет только `@psalm-internal` (включая namespace текущего класса)
  - `api` — добавляет `@api`

## Пример

До:

```php
namespace App\Service;

class ExampleClass
{
    // код
}
```

После применения фиксатора с настройкой `defaultVisibility: api`:

```php
namespace App\Service;

/**
 * @api
 */
class ExampleClass
{
    // код
}
```

После применения фиксатора с настройкой `defaultVisibility: internal+psalm-internal`:

```php
namespace App\Service;

/**
 * @internal
 * @psalm-internal App\Service
 */
class ExampleClass
{
    // код
}
```

## Лицензия

Этот проект распространяется под лицензией MIT.

## Автор

Разработано [Kenny1911](https://github.com/Kenny1911).

## Похожие проекты

- [typhoon/check-visibility-psalm-plugin](https://github.com/typhoon-php/check-visibility-psalm-plugin) - Плагин для
  Psalm, который предназначен для похожей цели. Можно использовать совместно с `ClassVisibilityFixer`.

