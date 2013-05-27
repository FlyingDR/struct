<?php

namespace Flying\Tests\Property\Stubs;

class ToString
{
    protected $_value;

    public function __construct($value)
    {
        $this->_value = $value;
    }

    public function toString()
    {
        return $this->_value;
    }

}
