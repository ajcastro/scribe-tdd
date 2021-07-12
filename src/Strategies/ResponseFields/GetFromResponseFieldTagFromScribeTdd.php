<?php

namespace AjCastro\ScribeTdd\Strategies\ResponseFields;

use Knuckles\Scribe\Extracting\Strategies\ResponseFields\GetFromResponseFieldTag;

class GetFromResponseFieldTagFromScribeTdd extends GetFromResponseFieldTag
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

        return $this->getResponseFieldsFromDocBlock($methodDocBlock->getTags(), $endpointData->responses);
    }
}
