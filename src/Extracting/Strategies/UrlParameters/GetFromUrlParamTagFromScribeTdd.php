<?php

namespace AjCastro\ScribeTdd\Extracting\Strategies\UrlParameters;

use AjCastro\ScribeTdd\TestResults\RouteTestResult;
use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Knuckles\Scribe\Extracting\RouteDocBlocker;
use Knuckles\Scribe\Extracting\Strategies\UrlParameters\GetFromUrlParamTag;

class GetFromUrlParamTagFromScribeTdd extends GetFromUrlParamTag
{
    public function __invoke(ExtractedEndpointData $endpointData, array $routeRules): ?array
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

        return $this->getUrlParametersFromDocBlock($methodDocBlock->getTags());
    }
}
