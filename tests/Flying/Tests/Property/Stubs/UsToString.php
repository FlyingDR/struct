<?php

namespace Flying\Tests\Property\Stubs;

class UsToString
{
    protected $_value;

    public function __construct($value)
    {
        $this->_value = $value;
    }

    public function __toString()
    {
        return $this->_value;
    }

}
