# Scribe TDD (Test-driven Documentation)

[Scribe](https://github.com/knuckleswtf/scribe)'s test-driven documentation approach.

## Installation and Setup


### Step 1: Composer Require
```
composer require ajcastro/scribe-tdd
```


### Step 2: Use ScribeTddSetup trait in TestCase

```php
use AjCastro\ScribeTdd\Tests\ScribeTddSetup;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, ScribeTddSetup;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpScribeTdd();
    }
}

```

### Step 3: Set the necessary strategies
```php
    'strategies' => [
        'metadata' => [
            // ...
            AjCastro\ScribeTdd\Extracting\Strategies\Metadata\GetFromDocBlocksFromScribeTdd::class,
        ],
        'urlParameters' => [
            // ...
        ],
        'queryParameters' => [
            // ...
        ],
        'headers' => [
            // ...
        ],
        'bodyParameters' => [
            // ...
        ],
        'responses' => [
            // ...
        ],
        'responseFields' => [
            // ...
        ],
    ],
```

## Usage

### Step 1: Create and run tests

Just create your usual phpunit tests and run them. This will generate the necessary files that will be
used for generating scribe documentation later.
```
phpunit
```

### Step 2: Run scribe:generate
```
php artisan scribe:generate
```

## Sample Usage

Here is a sample project where it uses the tdd approach:
[https://github.com/ajcastro/TheSideProjectAPI/pull/1](https://github.com/ajcastro/TheSideProjectAPI/pull/1)

## Acknowledgement
This package is inspired from [Enlighten](https://github.com/stydeNet/enlighten/).
