<?php

namespace AjCastro\ScribeTdd\Tests;

use AjCastro\ScribeTdd\Tests\ExampleRequest;
use AjCastro\ScribeTdd\Tests\Traits\SetProps;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Routing\Route;

class ExampleCreator implements Arrayable, Jsonable
{
    use SetProps;

    public $id;
    public $testClass;
    public $testMethod;
    public $dataName;
    public $providedData;
    public $description;
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
        $parts = array_filter([
            str_replace('\\', '~', $instance->testClass),
            $instance->testMethod,
            $instance->dataName,
        ]);

        return implode('--', $parts);
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
        $uri = str_replace('/', '~', $route->uri);
        $uri = str_replace('?', '.', $uri);

        $parts = [$uri];
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

    /** @deprecated in favor of getWritables() */
    public function writePath()
    {
        return static::writeDir($this->route).'/'.$this->id.'.json';
    }

    protected function getExtra()
    {
        return [
            'id'            => $this->id,
            'test_class'    => $this->testClass,
            'test_method'   => $this->testMethod,
            'data_name'     => $this->dataName,
            'provided_data' => $this->providedData,
            'description'   => $this->description,
            'key'           => $this->instanceKey(),
            'route' => [
                'uri'     => $this->route->uri,
                'name'    => $this->route->getName(),
                'methods' => $this->route->methods,
            ],
        ];
    }

    public function toArray()
    {
        return $this->getExtra() + [
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

    protected function mergeData($type)
    {
        $results = [];

        $method = 'get'.ucfirst($type);

        foreach ($this->exampleRequests as $request) {
            $results = $results + $request->{$method}();
        }

        return $results;
    }

    protected function mergeUrlParams()
    {
        return $this->mergeData('urlParams');
    }

    protected function mergeQueryParams()
    {
        return $this->mergeData('queryParams');
    }

    protected function mergeBodyParams()
    {
        return $this->mergeData('bodyParams');
    }

    protected function mergeResponses()
    {
        $results = [];

        foreach ($this->exampleRequests as $request) {
            $response = $request->getResponse();
            $description = $response['description'];

            if (!isset($results[$description])) {
                $results[$description] = $response;
            }
        }

        return array_values($results);
    }

    public function getWritables()
    {
        return [
            "00-extra-@{$this->testMethod}.json" => $this->getExtra(),
            "01-url_params-@{$this->testMethod}.json" => [
                'url_params' => $this->mergeUrlParams(),
            ],
            "02-query_params-@{$this->testMethod}.json" => [
                'query_params' => $this->mergeQueryParams(),
            ],
            "03-body_params-@{$this->testMethod}.json" => [
                'body_params' => $this->mergeBodyParams(),
            ],
            "04-responses-@{$this->testMethod}.json" => [
                'responses' => $this->mergeResponses(),
            ],
        ];
    }
}
