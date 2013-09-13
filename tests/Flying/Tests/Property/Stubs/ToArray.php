<?php

namespace Flying\Tests\Property\Stubs;

class ToArray
{
    protected $value;

    public function __construct(array $value)
    {
        $this->value = $value;
    }

    public function toArray()
    {
        return $this->value;
    }
}
