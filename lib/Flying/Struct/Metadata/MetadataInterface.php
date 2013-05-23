<?php

namespace Flying\Struct\Metadata;

/**
 * Interface for structure metadata classes
 */
interface MetadataInterface extends \Serializable
{

    /**
     * Get property name
     *
     * @return mixed
     */
    public function getName();

    /**
     * Set property name
     *
     * @param string $name
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function setName($name);

    /**
     * Get class name for property object
     *
     * @return string
     */
    public function getClass();

    /**
     * Set class name for property object
     *
     * @param string $class
     * @return $this
     */
    public function setClass($class);

    /**
     * Get configuration options for property object
     *
     * @return array
     */
    public function getConfig();

    /**
     * Set configuration options for property object
     *
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config);

    /**
     * Get metadata information as array
     *
     * @return array
     */
    public function toArray();

}
