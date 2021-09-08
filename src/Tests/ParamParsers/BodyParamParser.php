<?php

namespace AjCastro\ScribeTdd\Tests\ParamParsers;

use Illuminate\Support\Arr;

class BodyParamParser
{
    public static function parse($value)
    {
        if (is_array($value) && !empty($value)) {
            $value = head($value);

            return [
                'type' => gettype($value) . '[]',
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
}
