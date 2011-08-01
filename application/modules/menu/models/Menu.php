<?php
/**
 * @see Core_Db_Table_Row_Abstract
 */
require_once 'Core/Db/Table/Row/Abstract.php';

/**
 * Menu_Model_Menu
 *
 * @category    Application
 * @package     Menu_Model_Menu
 *
 * @author      Valeriu Baleyko <baleyko.v.v@gmail.com>
 * @author      Alexander Khaylo <alex.khaylo@gmail.com>
 * @copyright   Copyright (c) 2011 NIX Solutions (http://www.nixsolutions.com)
 */
class Menu_Model_Menu extends Core_Db_Table_Row_Abstract
{

    const TYPE_MVC          = 'mvc';
    const TYPE_URI          = 'uri';
    const TYPE_DEFAULT      = self::TYPE_MVC;

    const TARGET_NULL       = '';
    const TARGET_BLANK      = '_blank';
    const TARGET_PARENT     = '_parent';
    const TARGET_SELF       = '_self';
    const TARGET_TOP        = '_top';

    const ROUTE_TYPE_STATIC = 'static';
    const ROUTE_TYPE_MODULE = 'module';
    const ROUTE_TYPE_REGEX  = 'regex';
    const ROUTE_TYPE_ROUTE  = 'route';


    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->_data['label'];
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->_data['class'];
    }

    /**
     * Get Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_data['title'];
    }

    /**
     * Get link type
     *
     * @return string
     */
    public function getLinkType()
    {
        return $this->_data['type'];
    }

    /**
     * Get parent
     *
     * @return string
     */
    public function getParent()
    {
        return $this->_data['parentId'];
    }

    /**
     * Get route
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->_data['route'];
    }

    /**
     * Get uri
     *
     * @return string
     */
    public function getUri()
    {
        return $this->_data['uri'];
    }

    /**
     * Get visible
     *
     * @return string
     */
    public function getVisible()
    {
        return $this->_data['visible'];
    }

    /**
     * Get active
     *
     * @return string
     */
    public function getActive()
    {
        return $this->_data['active'];
    }

    /**
     * Get target
     *
     * @return string
     */
     public function getTarget()
     {
         return $this->_data['target'];
     }
}