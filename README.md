# Scribe TDD (Test-driven Documentation)

[Scribe](https://github.com/knuckleswtf/scribe)'s test-driven documentation approach.

# Benefits

- Better workflow, instead of writing docblock annotations for parameters in controller, you can auto-generate documentation from the tests performed.
- Less comments cluttering in controllers. Some annotations are still needed (like @group) but annotations can be put in the test classes instead.
- Easy to document controllers which methods are inherited from base controllers or traits by putting docblocks in the test methods.
- Follows the principle "If it is not tested, it does not exist.". This makes sure your docs and tests are in sync.
- It is easy to document responses because it is from the performed tests and does not rely on response calls which sometimes result to errors due to inconsistent database state.

## Installation and Setup


### Step 1: Composer Require
```
composer require --dev ajcastro/scribe-tdd
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
            AjCastro\ScribeTdd\Strategies\Metadata\GetFromDocBlocksFromScribeTdd::class,
        ],
        'urlParameters' => [
            // ...
            AjCastro\ScribeTdd\Strategies\UrlParameters\GetFromUrlParamTagFromScribeTdd::class,
        ],
        'queryParameters' => [
            // ...
            AjCastro\ScribeTdd\Strategies\QueryParameters\GetFromTestResult::class,
            AjCastro\ScribeTdd\Strategies\QueryParameters\AddPaginationParametersFromScribeTdd::class,
            AjCastro\ScribeTdd\Strategies\QueryParameters\GetFromQueryParamTagFromScribeTdd::class,
        ],
        'headers' => [
            // ...
            AjCastro\ScribeTdd\Strategies\Headers\GetFromHeaderTagFromScribeTdd::class,
        ],
        'bodyParameters' => [
            // ...
            AjCastro\ScribeTdd\Strategies\BodyParameters\GetFromTestResult::class,
            AjCastro\ScribeTdd\Strategies\BodyParameters\GetFromBodyParamTagFromScribeTdd::class,
        ],
        'responses' => [
            // ...
            AjCastro\ScribeTdd\Strategies\Responses\GetFromTestResult::class,
            AjCastro\ScribeTdd\Strategies\Responses\UseResponseTagFromScribeTdd::class,
            AjCastro\ScribeTdd\Strategies\Responses\UseResponseFileTagFromScribeTdd::class,
        ],
        'responseFields' => [
            // ...
            AjCastro\ScribeTdd\Strategies\ResponseFields\GetFromResponseFieldTagFromScribeTdd::class,
        ],
    ],
```
It is up to you if you want to disable existing default strategies or just add these strategies so you can enjoy both worlds.

## Usage

### Step 1: Create and run tests

Just create your usual phpunit tests and run them. This will generate the necessary files that will be
used for generating scribe documentation later.
```
phpunit
```

### Step 2: Run scribe:generate
Make sure to use `--force` to remove cached output.
```
php artisan scribe:generate --force
```

### Step 3: Gitignore auto-generated json files
Add the following to your `.gitignore` to ignore auto-generated json files.
You should commit your created files, those which are ending in `-@.json`, so that it will always be applied when generating api documentation.
```
storage/scribe-tdd/*/*
!storage/scribe-tdd/*/*-@.json
```
### Step 4: Delete auto-generated files (Optional)
When you run the `phpunit` tests, it creates a lot of files. You can delete these files when you already generated the api documentation by
running the command below. This will not delete your created files.
```
php artisan scribe:tdd:delete
```

## Sample Usage

Here is a sample project where it uses the tdd approach:
[https://github.com/ajcastro/TheSideProjectAPI/pull/1](https://github.com/ajcastro/TheSideProjectAPI/pull/1)

## Acknowledgement
This package is inspired from [Enlighten](https://github.com/stydeNet/enlighten/).
