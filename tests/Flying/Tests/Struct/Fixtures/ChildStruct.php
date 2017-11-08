<?php

namespace Flying\Tests\Struct\Fixtures;

/**
 * Child structure to test multi-level structures
 *
 * @property boolean $x
 * @Struct\Boolean(name="x", default=false)
 * @property int $y
 * @Struct\Integer(name="y", default=345)
 * @property string $z
 * @Struct\Str(name="z", default="string")
 */
class ChildStruct extends TestStruct
{
    /**
     * {@inheritdoc}
     */
    public function getExpectedContents()
    {
        return [
            'x' => false,
            'y' => 345,
            'z' => 'string',
        ];
    }
}
