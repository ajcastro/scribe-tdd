<?php

namespace AjCastro\ScribeTdd\Tests;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;
use AjCastro\ScribeTdd\Tests\ExampleRequest;
use AjCastro\ScribeTdd\Tests\Traits\SetProps;

class ExampleCreator implements Arrayable
{
    use SetProps;

    public $id;
    public $testClass;
    public $testMethod;
    public $providedData;
    public $dataName;

    private Route $route;
    private $exampleRequests;
    private $test;

    public static $currentInstance;

    public static $instances;

    public function __construct(array $props)
    {
        $this->setProps($props);

        $this->id = (string) Str::orderedUuid();

        $this->testClass = get_class($this->test);
    }

    public static function getCurrentInstance()
    {
        return static::$currentInstance;
    }

    public static function setCurrentInstance($instance)
    {
        static::$currentInstance = $instance;
    }

    public static function normalizeUriForInstanceKey(Route $route)
    {
        $parts = [
            str_replace('/', '~', $route->uri)
        ];
        $parts = array_merge($parts, $route->methods);

        return implode(',',  $parts);
    }

    public static function getInstanceForRoute($route)
    {
        if ($instance = static::$instances[static::normalizeUriForInstanceKey($route)] ?? null) {
            return $instance;
        }

        $instance = static::getCurrentInstance()->setRoute($route);

        return static::registerInstance($instance);
    }

    protected static function registerInstance($instance)
    {
        return static::$instances[$instance->instanceKey()] = $instance;
    }

    public static function getInstances()
    {
        return static::$instances;
    }

    public static function flushInstances()
    {
        static::$instances = [];
    }

    public function addExampleRequest(ExampleRequest $exampleRequest)
    {
        $this->exampleRequests[] = $exampleRequest;

        return $this;
    }

    public function setRoute(Route $route)
    {
        $this->route = $route;

        return $this;
    }

    public function instanceKey()
    {
        return $this->normalizeUriForInstanceKey($this->route);
    }

    public function writeDir()
    {
        return storage_path('scribe/'.$this->instanceKey());
    }

    public function toArray()
    {
        return [
            'id'            => $this->id,
            'test_class'    => $this->testClass,
            'test_method'   => $this->testMethod,
            'provided_data' => $this->providedData,
            'data_name'     => $this->dataName,
            'key'           => $this->instanceKey(),
            'route' => [
                'uri'     => $this->route->uri,
                'name'    => $this->route->getName(),
                'methods' => $this->route->methods,
            ],
        ];
    }

    public function mergeParamsExample($type)
    {
        $method = 'get'.ucfirst($type).'ParamsExample';
        $results = [];

        foreach ($exampleRequests as $request) {
            $results = array_merge($results, $request->{$method}());
        }

        return $results;
    }

    public function writeExampleRequests()
    {
        return [
            'urlParam' => [],
            'queryParam' => [],
            'bodyParam' => [],
            'responses' => [
                [
                    'status' => 200,
                    'scenario' => 'test_user_should_return_user',
                    'data' => [],
                ], [
                    'status' => 404,
                    'scenario' => 'test_user_not_found',
                    'data' => [],
                ],
            ],
        ];
    }
}
