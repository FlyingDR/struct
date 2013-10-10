<?php

namespace Flying\Struct\Annotation\Struct;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 * @Attributes({
 *      @Attribute("name", required=true, type="string"),
 *      @Attribute("type", required=false, type="string"),
 *      @Attribute("default", required=false, type="array"),
 *      @Attribute("nullable", required=false, type="boolean"),
 *      @Attribute("allowed", required=false, type="array")
 * })
 */
class Collection extends Property
{
}
