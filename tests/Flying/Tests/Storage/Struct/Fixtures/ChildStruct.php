<?php

namespace Flying\Tests\Storage\Struct\Fixtures;

/**
 * Child structure to test multi-level structures
 *
 * @property boolean $x
 * @Struct\Boolean(name="x", default=false)
 * @property int $y
 * @Struct\Int(name="y", default=345)
 * @property string $z
 * @Struct\String(name="z", default="string")
 */
class ChildStruct extends TestStruct
{

    /**
     * {@inheritdoc}
     */
    public function getExpectedContents()
    {
        return array(
            'x' => false,
            'y' => 345,
            'z' => 'string',
        );
    }

}
