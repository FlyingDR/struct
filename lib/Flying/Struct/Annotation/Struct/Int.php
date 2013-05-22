<?php

namespace Flying\Struct\Annotation\Struct;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Int extends Property
{
    /**
     * {@inheritdoc}
     */
    protected function getDefaultType()
    {
        return 'int';
    }

}
