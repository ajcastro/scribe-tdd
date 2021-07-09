<?php

namespace AjCastro\ScribeTdd\Tests;

use AjCastro\ScribeTdd\Exceptions\LaravelNotPresent;

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
            dump('making example...');
            $this->makeExample();
        });

        $this->beforeApplicationDestroyed(function () {
            dump('writing examples...');
            $instances = ExampleCreator::getInstances();
            foreach ($instances as $instance) {
                dump($instance->toArray());
            }
            ExampleCreator::flushInstances();
            // $this->saveExampleStatus();
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
