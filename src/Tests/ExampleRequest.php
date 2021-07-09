<?php

namespace AjCastro\ScribeTdd\Tests;

use Illuminate\Support\Str;

class ExampleRequest
{
    public $id;
    public $request;
    public $response;

    public function __construct($request, $response)
    {
        $this->id = (string) Str::orderedUuid();
        $this->request = $request;
        $this->response = $response;
    }

    public function getUrlParamsExample()
    {
        return [];
    }

    public function getQueryParamsExample()
    {
        return [];
    }

    public function getBodyParamsExample()
    {
        return $this->request->all();
    }

    public function getResponse()
    {
        return $this->response->getContent();
    }
}
