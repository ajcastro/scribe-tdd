<?php

namespace AjCastro\ScribeTdd\Tests\Traits;

trait SetProps
{
    public function setProps(array $props)
    {
        foreach ($props as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }
}
