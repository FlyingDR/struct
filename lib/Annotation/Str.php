<?php

namespace Flying\Struct\Annotation;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 * @Attributes({
 *      @Attribute("name", required=true, type="string"),
 *      @Attribute("default", required=false, type="string"),
 *      @Attribute("nullable", required=false, type="boolean"),
 *      @Attribute("maxlength", required=false, type="int")
 * })
 */
class Str extends Property
{
}
