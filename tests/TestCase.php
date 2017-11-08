<?php

namespace Flying\Tests;

use Mockery;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        // Integrate with Mockery
        Mockery::close();
    }
}
