<?php

namespace AjCastro\ScribeTdd\Tests;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExampleRequest
{
    public $id;
    public $request;
    public $response;

    public function __construct(Request $request, $response)
    {
        $this->id = (string) Str::orderedUuid();
        $this->request = $request;
        $this->response = $response;
    }

    public function getUrlParams()
    {
        return collect($this->request->route()->parameters())->map(function ($value, $key) {
            $value = method_exists($value, 'getKey') ? $value->getKey() : $value;
            return [
                'type' =>   gettype($value),
                'description' => '',
                'example' => $value,
                'required' => $this->isUrlParamRequired($key),
            ];
        })->all();
    }

    private function isUrlParamRequired($key)
    {
        return Str::contains($this->request->route()->uri, '{'.$key.'}');
    }

    public function getQueryParams()
    {
        return collect()->wrap($this->request->query->all())->map([$this, 'mapParams'])->all();
    }

    public function getBodyParams()
    {
        return collect()->wrap($this->request->request->all())->map([$this, 'mapParams'])->all();
    }

    public function mapParams($value)
    {
        if (is_array($value) && !empty($value)) {
            $value = head($value);

            return [
                'type' =>   gettype($value).'[]',
                'description' => '',
                'example' => [$value],
                'required' => false,
            ];
        }

        return [
            'type' =>   gettype($value),
            'description' => '',
            'example' => $value,
            'required' => false,
        ];
    }

    public function getResponse()
    {
        return [];
        return $this->response->getContent();
    }
}
