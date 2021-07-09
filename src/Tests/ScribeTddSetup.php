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
            $instances = ExampleCreator::getInstances();
            foreach ($instances as $instance) {
                File::makeDirectory($instance->writeDir($instance->route), 0755, true, true);
                File::put($instance->writePath(), $instance->toJson());
            }
            ExampleCreator::flushInstances();
        });
    }

    private function makeExample(): void
    {
        $exampleCreator = new ExampleCreator([
            'test'         => $this,
            'testMethod'   => $this->getName(false),
            'providedData' => $this->getProvidedData(),
            'dataName'     => $this->dataName(),
        ]);

        ExampleCreator::setCurrentInstance($exampleCreator);
    }
}
