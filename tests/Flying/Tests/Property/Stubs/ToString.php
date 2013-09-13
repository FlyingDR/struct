<?php

namespace Flying\Tests\Property\Stubs;

class ToString
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function toString()
    {
        return $this->value;
    }
}
