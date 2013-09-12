<?php

namespace Flying\Struct\Annotation\Struct;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 * @Attributes({
 * @Attribute("name", required=true, type="string"),
 * @Attribute("type", required=false, type="string"),
 * @Attribute("default", required=false, type="int"),
 * @Attribute("nullable", required=false, type="boolean"),
 * @Attribute("min", required=false, type="int"),
 * @Attribute("max", required=false, type="int")
 * })
 */
class Int extends Property
{

}
