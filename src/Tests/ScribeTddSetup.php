<?php

namespace AjCastro\ScribeTdd\Tests;

use AjCastro\ScribeTdd\Exceptions\LaravelNotPresent;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\File;
use PHPUnit\Util\Test as TestUtil;
use Str;

trait ScribeTddSetup
{
    public function setUpScribeTdd(): void
    {
        if (! config('scribe-tdd.enabled')) {
            return;
        }

        if (empty($this->app)) {
            throw new LaravelNotPresent;
        }

        $this->afterApplicationCreated(function () {
            if (!$this->shouldSkipExample()) {
              $this->makeExample();
            }
        });

        $this->beforeApplicationDestroyed(function () {
            $this->writeExample();
        });
    }

    private function makeExample(): void
    {
        /** @var TestCase $this */
        $exampleCreator = new ExampleCreator([
            'test'         => $this,
            'testMethod'   => $this->getName(false),
            'dataName'     => $this->dataName(),
            'providedData' => $this->getProvidedData(),
            'description'  => $this->guessResponseDescription($this->getName(false)),
        ]);

        ExampleCreator::setCurrentInstance($exampleCreator);
    }

    private function writeExample()
    {
        $instances = ExampleCreator::getInstances();
        foreach ($instances as $instance) {
            $writeDir = $instance->writeDir($instance->route);
            File::makeDirectory($writeDir, 0755, true, true);

            foreach ($instance->getWritables() as $filename => $writeData) {
                File::put($writeDir."/".$filename, json_encode($writeData, JSON_PRETTY_PRINT));
            };
        }

        ExampleCreator::flushInstances();
    }

    private function shouldSkipExample(): bool
    {
        return !is_null($this->getAnnotation($this->getName(false), 'scribeSkip'));
    }

    private function guessResponseDescription($testMethod)
    {
        $description = $this->getAnnotation($testMethod, 'scribeDescription')[0] ?? null;

        if ($description) {
            return $description;
        }

        if (Str::startsWith($testMethod, 'test')) {
            $testMethod = substr($testMethod, 4);
        }

        return trim(str_replace('_', ' ', Str::snake($testMethod)));
    }

    private function getAnnotation($testMethod, $name): ?array
    {
        $annotations = TestUtil::parseTestMethodAnnotations(
            static::class,
            $testMethod
        );
        
        return $annotations['method'][$name] ?? null;
    }
}
