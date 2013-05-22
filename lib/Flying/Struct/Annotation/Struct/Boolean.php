<?php

namespace Flying\Struct\Annotation\Struct;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Boolean extends Property
{
    /**
     * {@inheritdoc}
     */
    protected function getDefaultType()
    {
        return 'boolean';
    }

}
