<?php

/**
 * This file is part of the Boomgo PHP ODM.
 *
 * http://boomgo.org
 * https://github.com/Retentio/Boomgo
 *
 * (c) Ludovic Fleury <ludo.fleury@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Boomgo\Map;

/**
 * DocumentMap
 *
 * @author Ludovic Fleury <ludo.fleury@gmail.com>
 */
class Map
{
    /**
     * @var string The mapped FQDN
     */
    private $class;

    /**
     * @var array Indexed by "PHP attributes" where value are "MongoDB keys"
     */
    private $phpIndex;

    /**
     * @var array Indexed by "MongoDB keys" where are "PHP attributes"
     */
    private $mongoIndex;

    /**
     * @var array Indexed by "PHP attributes"
     */
    private $definitions;

    /**
     * @var array Indexed by "PHP attributes"
     */
    private $dependencies;

    /**
     * Constructor
     *
     * @param string $class The mapped FQDN
     */
    public function __construct($class)
    {
        $this->class = $class;
        $this->phpIndex = array();
        $this->mongoIndex = array();
        $this->definitions = array();
        $this->dependencies = array();
    }

    /**
     * Returns the FQDN of the mapped class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Returns the php indexed map
     *
     * Reversed representation of the mongoIndex array (no need to flip it)
     * Array keys are php attribute & values are mongo key.
     *
     * @example array('phpAttribute' => 'mongoKey');
     * @return  array
     */
    public function getPhpIndex()
    {
        return $this->phpIndex;
    }

    /**
     * Returns the mongo indexed map
     *
     * Reversed representation of the phpIndex array (no need to flip it)
     * Array keys are mongo keys & values are php attribute.
     *
     * @example array('mongoKey' => 'phpAttribute');
     * @return  array
     */
    public function getMongoIndex()
    {
        return $this->mongoIndex;
    }

    /**
     * Returns a mongo key indexed array of embedded maps
     *
     * @return array
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }

    /**
     * Returns a mongo key indexed array of embedded maps
     *
     * @return array
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * Add a definition
     *
     * @param Definition $definition
     */
    public function add(Definition $definition)
    {
        $attribute = $definition->getAttribute();
        $key = $definition->getKey();

        $this->phpIndex[$attribute] = $key;
        $this->mongoIndex[$key] = $attribute;

        $this->definitions[$attribute] = $definition;
    }

    /**
     * Check if a definition exists
     *
     * @param  string $identifier
     * @return boolean
     */
    public function has($identifier)
    {
        return isset($this->phpIndex[$identifier]) || isset($this->mongoIndex[$identifier]);
    }

    /**
     * Return a definition
     *
     * @param  string $identifier
     * @return mixed  null|Definition
     */
    public function get($identifier)
    {
        // Identifier is a php attribute
        if (isset($this->phpIndex[$identifier])) {
            return $this->definitions[$identifier];
        }

        // Identifier is a MongoDB Key
        if (isset($this->mongoIndex[$identifier])) {
            return $this->definitions[$this->mongoIndex[$identifier]];
        }

        return null;
    }

    /**
     * Adds an embedded map for a mongo key
     *
     * @param string $attribute
     * @param Map    $map
     */
    public function addDependency($attribute, Map $map)
    {
        if (!isset($this->phpAttribute[$attribute])) {
            throw new \InvalidArgumentException(sprintf('Unable to add dependency for an un-mapped php attribute "%s"', $attribute));
        }
        $this->dependencies[$attribute] = $map;
    }

    /**
     * Checks if an embedded map exists for a mongo key
     *
     * @param  string  $key
     * @return boolean
     */
    public function hasDependency($attribute)
    {
        return isset($this->dependencies[$attribute]);
    }

    /**
     * Returns an embedded map for a mongo key
     *
     * @param  string $key
     * @return mixed  null|Map
     */
    public function getDependency($attribute)
    {
        return ($this->dependencies($attribute)) ? $this->dependencies[$attribute] : null;
    }
}