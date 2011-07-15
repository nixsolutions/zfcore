<?php
/**
 * Class Options_Model_Options_Manager
 *
 * for options table management
 *
 * @category Application
 * @package  Model
 *
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 *
 * @version  $Id: Option.php 207 2010-10-20 15:37:18Z AntonShevchuk $
 */
class Options_Model_Options_Manager extends Core_Model_Manager
{
    const TYPE_INT    = 'int';
    const TYPE_FLOAT  = 'float';
    const TYPE_STRING = 'string';
    const TYPE_ARRAY  = 'array';
    const TYPE_OBJECT = 'object';

    static protected $_instance = null;

    static protected $_cache = array();

    /**
     * Get instance
     *
     * @return Options_Model_Options_Manager
     */
    static public function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Clear cache
     *
     * @param string $key
     * @param string $namespace
     * @return void
     */
    static public function clearCache($key = null, $namespace = 'default')
    {
        if (null !== $key && isset(self::$_cache[$namespace][$key])) {
            unset(self::$_cache[$namespace][$key]);
        } elseif (isset(self::$_cache[$namespace])) {
            unset(self::$_cache[$namespace]);
        }
    }

    /**
     * Get option
     *
     * @param  string $key
     * @param  string $namespace
     * @return mixed
     */
    static public function get($key, $namespace = 'default')
    {
        if (!isset(self::$_cache[$namespace][$key])) {
            $self = Options_Model_Options_Manager::getInstance();
            $row = $self->getDbTable()->getOption($key, $namespace);

            if ($row) {
                self::$_cache[$namespace][$key] = $row->getValue();
            } else {
                self::$_cache[$namespace] = false;
            }
        }
        return self::$_cache[$namespace][$key];
    }

    /**
     * Set option
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  string $namespace
     * @param  string $type
     * @return void
     */
    static public function set($key, $value, $namespace = 'default', $type = null)
    {
        self::$_cache[$namespace][$key] = $value;
        $self = Options_Model_Options_Manager::getInstance();
        $self->getDbTable()->setOption($key, $value, $namespace, $type);
    }

    /**
     * Delete option
     *
     * @param string $key
     * @param string $namespace
     */
    static public function delete($key, $namespace = 'default')
    {
        $self = Options_Model_Options_Manager::getInstance();
        $self->getDbTable()->deleteOption($key, $namespace);

        if (isset(self::$_cache[$namespace][$key])) {
            unset(self::$_cache[$namespace][$key]);
        }
    }

    /**
     * Get all options from namespace
     *
     * @param string $namespace
     * @return array
     */
    static public function getNamespace($namespace)
    {
        $self = Options_Model_Options_Manager::getInstance();
        return $self->getOptions($namespace);
    }

    /**
     * Set namespace
     *
     * @param string $namespace
     * @param array $options
     */
    static public function setNamespace($namespace, array $options)
    {
        $self = Options_Model_Options_Manager::getInstance();
        return $self->setOptions($options, $namespace);
    }

    /**
     * Get Options
     *
     * @param string $namespace
     * @return array
     */
    public function getOptions($namespace)
    {
        if (!isset($this->_cache[$namespace])) {
            self::$_cache[$namespace] = array();

            $rowset = $this->getDbTable()->getByNamespace($namespace);
            foreach ($rowset as $row) {
                self::$_cache[$namespace][$row->name] = $row->getValue();
            }
        }
        return self::$_cache[$namespace];
    }

    /**
     * Add options
     *
     * @param array $options
     * @param string $namespace
     * @return void
     */
    public function setOptions(array $options, $namespace = 'default')
    {
        $table = Options_Model_Options_Manager::getInstance()->getDbTable();
        foreach ($options as $key => $value) {
            $table->setOption($key, $value, $namespace);
        }
    }

    /**
     * Delete all options by namespace
     *
     * @param string $namespace
     * @return void
     */
    static public function deleteNamespace($namespace)
    {
        $self = Options_Model_Options_Manager::getInstance();
        $self->getDbTable()->deleteNamespace($namespace);
        if (isset(self::$_cache[$namespace])) {
            unset(self::$_cache[$namespace]);
        }
    }
}
