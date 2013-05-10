<?php

namespace Flying\Struct\Configuration;

use Flying\Struct\Exception;

/**
 * Class namespaces management functionality for structures configuration
 */
class NamespacesMap
{
    /**
     * List of registered namespaces
     * @var array
     */
    protected $_namespaces = array();

    /**
     * Get registered namespace by given alias
     *
     * @param string $alias     Namespace alias
     * @throws Exception
     * @return string
     */
    public function get($alias)
    {
        if (!$this->has($alias)) {
            throw new Exception('Class namespace with alias "' . $alias . '" is not registered');
        }
        return ($this->_namespaces[$alias]);
    }

    /**
     * Get all registered namespaces
     *
     * @return array
     */
    public function getAll()
    {
        return ($this->_namespaces);
    }

    /**
     * Check if namespace with given alias is registered
     *
     * @param string $alias     Namespace alias
     * @return boolean
     */
    public function has($alias)
    {
        return (array_key_exists($alias, $this->_namespaces));
    }

    /**
     * Register class namespace
     *
     * @param string $alias         Namespace alias
     * @param string $namespace     Class namespace
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function add($alias, $namespace)
    {
        if ((!is_string($alias)) || (!strlen($alias))) {
            throw new \InvalidArgumentException('Class namespace alias must be a string');
        }
        if ((!is_string($namespace)) || (!strlen($namespace))) {
            throw new \InvalidArgumentException('Class namespace must be a string');
        }
        $this->_namespaces[$alias] = trim($namespace, '\\');
        return ($this);
    }

    /**
     * Remove namespace with given alias from list of registered namespaces
     *
     * @param string $alias         Namespace alias
     * @return $this
     */
    public function remove($alias)
    {
        unset($this->_namespaces[$alias]);
        return ($this);
    }

}
