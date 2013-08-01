<?php

namespace Flying\Struct\Annotation\Struct;

/**
 * @Annotation
 * @Target({"CLASS"})
 * @Attributes({
 * @Attribute("name", required=true, type="string"),
 * @Attribute("type", required=false, type="string"),
 * @Attribute("default", required=false, type="mixed"),
 * @Attribute("nullable", required=false, type="boolean"),
 * @Attribute("values", required=true, type="array")
 * })
 */
class Enum extends Property
{
    /**
     * {@inheritdoc}
     */
    protected function getDefaultType()
    {
        return 'enum';
    }

}
