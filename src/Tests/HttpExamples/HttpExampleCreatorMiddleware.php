<?php

namespace AjCastro\ScribeTdd\Tests\HttpExamples;

use Closure;
use AjCastro\ScribeTdd\Tests\ExampleCreator;
use AjCastro\ScribeTdd\Tests\ExampleRequest;

class HttpExampleCreatorMiddleware
{
    public function handle($request, Closure $next)
    {
        // Some controllers are merging other params in the request
        // so we only get the original body and query params to display in the docs.
        $originalBodyParams = $request->request->all();
        $originalQueryParams = $request->query->all();

        $response = $next($request);

        $request->request->replace($originalBodyParams);
        $request->query->replace($originalQueryParams);

        $exampleCreator = ExampleCreator::getInstanceForRoute($request->route());

        $exampleCreator->addExampleRequest(new ExampleRequest($request, $response, $exampleCreator));

        return $response;
    }
}
