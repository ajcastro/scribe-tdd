<?php

namespace AjCastro\ScribeTdd\Tests\ParamParsers;

use Illuminate\Support\Arr;

class QueryParamParser
{
    public static function parse($value)
    {
        // case 1, scalar values
        if (is_scalar($value)) {
            return [
                'type' => gettype($value),
                'description' => '',
                'example' => $value,
                'required' => false,
            ];
        }

        // case 2, array of scalar values
        if (is_array($value) && !Arr::isAssoc($value)) {
            return [
                'type' => gettype(head($value)) . '[]',
                'description' => '',
                'example' => $value,
                'required' => false,
            ];
        }
    }
}
