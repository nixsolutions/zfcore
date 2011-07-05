<?php
/**
 * Helper_Options
 *
 * @version $Id$
 */
class Helper_Options extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Direct
     *
     * @param  string $key
     * @param  string $namespace
     * @return mixed
     */
    public function direct($key, $namespace = 'default')
    {
        return $this->get($key, $namespace);
    }

    /**
     * Get all options from namespace
     *
     * @param string $namespace
     * @return array
     */
    public function getNamespace($namespace)
    {
        return Options_Model_Options_Manager::getNamespace($namespace);
    }

    /**
     * Set namespace
     *
     * @param string $namespace
     * @param array $options
     * @return void
     */
    public function setNamespace($namespace, array $options)
    {
        Options_Model_Options_Manager::getNamespace($namespace, $options);
    }

    /**
     * Get option
     *
     * @param  string $key
     * @param  string $namespace
     * @return mixed
     */
    public function get($key, $namespace = 'default')
    {
        return Options_Model_Options_Manager::get($key, $namespace);
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
    public function set($key, $value, $namespace = 'default', $type = null)
    {
        Options_Model_Options_Manager::set($key, $value, $namespace, $type);
    }

}