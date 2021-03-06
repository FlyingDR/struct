<?php

namespace Flying\Struct;

/**
 * Structures configuration manager.
 *
 * The only purpose of this class is to define a point
 * where newly created structure classes will be able to take
 * information about structures configuration
 */
class ConfigurationManager
{
    /**
     * Structures configuration
     *
     * @var Configuration
     */
    private static $configuration;

    /**
     * Get structures configuration class
     *
     * @return Configuration
     */
    public static function getConfiguration()
    {
        if (!self::$configuration instanceof Configuration) {
            self::setConfiguration(new Configuration());
        }
        return self::$configuration;
    }

    /**
     * Set structures configuration class
     *
     * @param Configuration $configuration
     * @return void
     */
    public static function setConfiguration(Configuration $configuration)
    {
        self::$configuration = $configuration;
    }
}
