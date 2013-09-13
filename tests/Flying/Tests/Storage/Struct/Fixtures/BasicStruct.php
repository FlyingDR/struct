<?php

namespace Flying\Tests\Storage\Struct\Fixtures;

/**
 * Basic test structure
 *
 * @property boolean $first
 * @Struct\Boolean(name="first", default=true)
 * @property int $second
 * @Struct\Int(name="second", nullable=false, default=100, min=10, max=1000)
 * @property string $third
 * @Struct\String(name="third")
 * @property string $fourth
 * @Struct\Property(name="fourth", type="string", default="some value")
 */
class BasicStruct extends TestStruct
{
    /**
     * {@inheritdoc}
     */
    public function getExpectedContents()
    {
        return (array(
            'first'  => true,
            'second' => 100,
            'third'  => null,
            'fourth' => 'some value',
        ));
    }
}
