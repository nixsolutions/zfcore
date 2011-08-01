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
    const TYPE_DEFAULT      = 'mvc';
    const TYPE_MVC          = 'mvc';
    const TYPE_URI          = 'uri';

    const TARGET_NULL       = '';
    const TARGET_BLANK      = '_blank';
    const TARGET_PARENT     = '_parent';
    const TARGET_SELF       = '_self';
    const TARGET_TOP        = '_top';

    const ROUTE_TYPE_STATIC = 'static';
    const ROUTE_TYPE_MODULE = 'module';
    const ROUTE_TYPE_REGEX  = 'regex';
    const ROUTE_TYPE_ROUTE  = 'route';


    public function getLabel()
    {
        return (string) $this->_data['label'];
    }

    public function getClass()
    {
        return (string) $this->_data['class'];
    }

    public function getTitle()
    {
        return (string) $this->_data['title'];
    }

    public function getLinkType()
    {
        if ($this->_data['type'] == self::TYPE_MVC) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getParent()
    {
        return (integer) $this->_data['parentId'];
    }

    public function getRoute()
    {
        return (string) $this->_data['route'];
    }

    public function getUri()
    {
        return (string) $this->_data['uri'];
    }

    public function getVisible()
    {
        return (integer) $this->_data['visible'];
    }

    public function getActive()
    {
        return (integer) $this->_data['active'];
    }

    /**
     * get target
     *
     * @return string
     */
     public function getTarget()
     {
         return (string) $this->_data['target'];
     }
}