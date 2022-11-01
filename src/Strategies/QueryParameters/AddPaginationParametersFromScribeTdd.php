<?php

namespace AjCastro\ScribeTdd\Strategies\QueryParameters;

use AjCastro\ScribeTdd\TestResults\RouteTestResult;
use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Knuckles\Scribe\Extracting\ParamHelpers;
use Knuckles\Scribe\Extracting\RouteDocBlocker;
use Knuckles\Scribe\Extracting\Strategies\Strategy;

class AddPaginationParametersFromScribeTdd extends Strategy
{
    use ParamHelpers;

    public function __invoke(ExtractedEndpointData $endpointData, array $routeRules = []): ?array
    {
        $testResult = RouteTestResult::getTestResultForRoute($endpointData->route);

        if (empty($testResult)) {
            return [];
        }

        [
            'method' => $methodDocBlock,
        ]
        = RouteDocBlocker::getDocBlocks($endpointData->route, [
            $testResult['test_class'],
            $testResult['test_method'],
        ]);

        $tags = $methodDocBlock->getTagsByName('usesPagination');

        if (empty($tags)) {
            // Doesn't use pagination
            return [];
        }

        return [
            'page' => [
                'description' => 'Page number to return.',
                'required' => false,
                'example' => 1,
            ],
            'per_page' => [
                'description' => 'Number of items to return in a page. Defaults to 10.',
                'required' => false,
                'example' => null, // So it doesn't get included in the examples
            ],
        ];
    }
}
