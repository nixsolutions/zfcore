<?php
/**
 * @see Core_Db_Table_Row_Abstract
 */
require_once 'Core/Db/Table/Row/Abstract.php';

/**
 * Menus_Model_Menu
 *
 * @category    Application
 * @package     Menus_Model_Menu
 *
 * @author      Valeriu Baleyko <baleyko.v.v@gmail.com>
 * @copyright   Copyright (c) 2010 NIX Solutions (http://www.nixsolutions.com)
 */
class Menus_Model_Menu extends Core_Db_Table_Row_Abstract
{
    const TYPE_DEFAULT = 'mvc';
    const TYPE_MVC = 'mvc';
    const TYPE_URI = 'uri';

    const TARGET_NULL = '';
    const TARGET_BLANK = '_blank';
    const TARGET_PARENT = '_parent';
    const TARGET_SELF = '_self';
    const TARGET_TOP = '_top';

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

    public function getType()
    {
        return (string) $this->_data['type'];
    }

    public function getParent()
    {
        return (integer) $this->_data['parent_id'];
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
    /**
     * get target
     *
     * @return string
     */
     public function getTarget()
     {
         return (string) $this->_data['target'];
     }

    /**
     * set target
     *
     * @param string $target
     * @return boolean true|false
     */
     public function setTarget($target)
     {
         if ($target != TARGET_BLANK &&
             $target != TARGET_PARENT &&
             $target != TARGET_SELF &&
             $target != TARGET_TOP
                 ) {
            $this->type = self::TARGET_NULL;
         }

         $this->target = (string) $target;
         return true;
     }

    /**
     * set type
     *
     * @param string $type
     * @return string
     */
     public function setType($type)
     {
         if ($type != TYPE_MVC &&
             $type != TYPE_URI
                 ) {
             $this->type = self::TYPE_DEFAULT;
         }

         $this->type = (string) $type;
         return true;
     }

    /**
     * get params
     *
     * @return array
     */
    public function getParams()
    {
        return json_decode($this->params);
    }

    /**
     * get params
     *
     * @param array $params
     * @return boolean true|false
     */
    public function setParams(array $params = null)
    {
        $this->params = (string) json_encode($params);
    }
}