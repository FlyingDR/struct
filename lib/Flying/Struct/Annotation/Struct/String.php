<?php

namespace Flying\Struct\Annotation\Struct;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class String extends Property
{
    /**
     * {@inheritdoc}
     */
    protected function getDefaultType()
    {
        return 'string';
    }

}
