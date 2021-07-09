<?php

namespace AjCastro\ScribeTdd\Tests\HttpExamples;

use Closure;
use Illuminate\Testing\TestResponse;
use AjCastro\ScribeTdd\Tests\ExampleCreator;
use AjCastro\ScribeTdd\Tests\ExampleRequest;

class HttpExampleCreatorMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $exampleCreator = ExampleCreator::getInstanceForRoute($request->route());

        $exampleCreator->addExampleRequest(new ExampleRequest($request, $response));

        return $response;
    }
}
