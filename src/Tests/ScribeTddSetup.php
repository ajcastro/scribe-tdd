<?php

namespace AjCastro\ScribeTdd\Tests;

use AjCastro\ScribeTdd\Exceptions\LaravelNotPresent;
use Illuminate\Support\Facades\File;

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
            $this->makeExample();
        });

        $this->beforeApplicationDestroyed(function () {
            $this->writeExample();
        });
    }

    private function makeExample(): void
    {
        $exampleCreator = new ExampleCreator([
            'test'         => $this,
            'testMethod'   => $this->getName(false),
            'dataName'     => $this->dataName(),
            'providedData' => $this->getProvidedData(),
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
}
