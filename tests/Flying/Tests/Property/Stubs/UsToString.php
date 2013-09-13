<?php

namespace Flying\Tests\Property\Stubs;

class UsToString
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return $this->value;
    }
}
