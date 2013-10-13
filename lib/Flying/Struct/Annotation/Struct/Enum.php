<?php

namespace Flying\Struct\Annotation\Struct;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 * @Attributes({
 *      @Attribute("name", required=true, type="string"),
 *      @Attribute("default", required=false, type="mixed"),
 *      @Attribute("nullable", required=false, type="boolean"),
 *      @Attribute("values", required=true, type="array")
 * })
 */
class Enum extends Property
{
}
