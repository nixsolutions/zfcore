<?php

require_once 'Zend/Tool/Project/Provider/Abstract.php';

/**
 * Abstract Core Provider
 *
 * @category Core
 * @package  Core_Tool
 *
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 */
class Core_Tool_Project_Provider_Abstract
    extends Zend_Tool_Project_Provider_Abstract
{
    /**
     * constructor
     */
    public function initialize()
    {
        parent::initialize();

        return ;
        // load Core Context
        $contextRegistry = Zend_Tool_Project_Context_Repository::getInstance();
        $contextRegistry->addContextsFromDirectory(
            dirname(dirname(__FILE__)) . '/Context/Core/',
            'Core_Tool_Project_Context_Core_'
        );
    }
}