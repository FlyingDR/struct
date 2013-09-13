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
    protected $namespaces = array();

    /**
     * Class constructor
     *
     * @param array|string $namespaces  OPTIONAL Namespaces to register
     */
    public function __construct($namespaces = null)
    {
        $this->namespaces = array();
        if ($namespaces) {
            $this->add($namespaces);
        }
    }

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
        return ($this->namespaces[$alias]);
    }

    /**
     * Get all registered namespaces
     *
     * @return array
     */
    public function getAll()
    {
        return ($this->namespaces);
    }

    /**
     * Check if namespace with given alias is registered
     *
     * @param string $alias     Namespace alias
     * @return boolean
     */
    public function has($alias)
    {
        return (array_key_exists($alias, $this->namespaces));
    }

    /**
     * Register class namespace
     *
     * @param string|array $namespace     Class namespace
     * @param string $alias               OPTIONAL Namespace alias
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function add($namespace, $alias = null)
    {
        if (!is_array($namespace)) {
            if ($namespace !== null) {
                $namespace = ($alias !== null) ? array($alias => $namespace) : array($namespace);
            } else {
                $namespace = array();
            }
        }
        foreach ($namespace as $alias => $ns) {
            if ((!is_string($ns)) || (!strlen($ns))) {
                throw new \InvalidArgumentException('Class namespace must be a string');
            }
            if ((!is_string($alias)) || (!strlen($alias))) {
                $alias = mb_strtolower(str_replace('\\', '_', $ns));
            }
            $ns = trim($ns, '\\');
            $this->namespaces[$alias] = $ns;
        }
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
        unset($this->namespaces[$alias]);
        return ($this);
    }
}
