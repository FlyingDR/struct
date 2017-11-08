<?php

namespace Flying\Tests\Struct\Fixtures;

/**
 * Multi-level structure with child structure
 *
 * @property boolean $b
 * @Struct\Boolean(name="b", default=true)
 * @property int $i
 * @Struct\Integer(name="i", default=123)
 * @property string $s
 * @Struct\Str(name="s", default="test")
 * @property ChildStruct $child
 * @Struct\Struct(name="child", class="ChildStruct")
 */
class MultiLevelStruct extends TestStruct
{
    /**
     * {@inheritdoc}
     */
    public function getExpectedContents()
    {
        $contents = [
            'b' => true,
            'i' => 123,
            's' => 'test',
        ];
        $child = new ChildStruct();
        $contents['child'] = $child->getExpectedContents();
        return $contents;
    }
}
