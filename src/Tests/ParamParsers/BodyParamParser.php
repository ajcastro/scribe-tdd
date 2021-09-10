<?php

namespace AjCastro\ScribeTdd\Tests\ParamParsers;

use Illuminate\Support\Arr;

class BodyParamParser
{
    public static function parse($value)
    {
        return QueryParamParser::parse($value);
    }
}
