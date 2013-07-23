<?php

namespace Flying\Tests\Property\Stubs;

class ToArray
{
    protected $_value;

    public function __construct(array $value)
    {
        $this->_value = $value;
    }

    public function toArray()
    {
        return $this->_value;
    }

}
