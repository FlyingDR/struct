<?php

namespace Flying\Struct\Annotation\Struct;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 * @Attributes({
 * @Attribute("name", required=true, type="string"),
 * @Attribute("type", required=false, type="string"),
 * @Attribute("default", required=false, type="string"),
 * @Attribute("nullable", required=false, type="boolean"),
 * @Attribute("maxlength", required=false, type="int")
 * })
 */
class String extends Property
{

}
