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
    const TYPE_DEFAULT = 'uri';
    const TYPE_MVC = 'mvc';
    const TYPE_URI = 'uri';

    const TARGET_NULL = '';
    const TARGET_BLANK = '_blank';
    const TARGET_PARENT = '_parent';
    const TARGET_SELF = '_self';
    const TARGET_TOP = '_top';

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
        return (integer) $this->_data['parent'];
    }

    public function getModule()
    {
        return (string) $this->_data['module'];
    }

    public function getController()
    {
        return (string) $this->_data['controller'];
    }

    public function getAction()
    {
        return (string) $this->_data['action'];
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
         if ($target != TYPE_MVC &&
             $target != TYPE_URI
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