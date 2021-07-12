<?php

namespace AjCastro\ScribeTdd\Tests;

use AjCastro\ScribeTdd\Tests\ExampleRequest;
use AjCastro\ScribeTdd\Tests\Traits\SetProps;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;

class ExampleCreator implements Arrayable, Jsonable
{
    use SetProps;

    public $id;
    public $testClass;
    public $testMethod;
    public $providedData;
    public $dataName;
    public Route $route;

    private $exampleRequests;
    private $test;

    public static $currentInstance;

    public static $instances = [];

    public function __construct(array $props)
    {
        $this->setProps($props);
        $this->testClass = get_class($this->test);
        $this->id = static::makeId($this);
    }

    public static function makeId(self $instance)
    {
        return str_replace('\\', '~', $instance->testClass).'--'.$instance->testMethod;
    }

    public static function getCurrentInstance()
    {
        return static::$currentInstance;
    }

    public static function setCurrentInstance(self $instance)
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

    public static function writeDir(Route $route)
    {
        return storage_path('scribe-tdd/'.static::normalizeUriForInstanceKey($route));
    }

    public static function getInstanceForRoute($route)
    {
        if ($instance = static::$instances[static::normalizeUriForInstanceKey($route)] ?? null) {
            return $instance;
        }

        $instance = static::getCurrentInstance()->setRoute($route);

        return static::registerInstance($instance);
    }

    protected static function registerInstance(self $instance)
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

    public function writePath()
    {
        return static::writeDir($this->route).'/'.$this->id.'.json';
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
            'url_params'   => $this->mergeUrlParams(),
            'query_params' => $this->mergeQueryParams(),
            'body_params'  => $this->mergeBodyParams(),
            'responses'    => $this->mergeResponses(),
        ];
    }

    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    protected function mergeUrlParams()
    {
        return [];
    }

    protected function mergeQueryParams()
    {
        return [];
    }

    protected function mergeBodyParams()
    {
        return [];
    }

    protected function mergeResponses()
    {
        return [];
    }

    public function writeExampleRequests()
    {
        return [
            'urlParams' => [],
            'queryParams' => [],
            'bodyParams' => [],
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
