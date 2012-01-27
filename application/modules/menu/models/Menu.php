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
 * @author      Alexander Khaylo <alex.khaylo@gmail.com>
 * @copyright   Copyright (c) 2012 NIX Solutions (http://www.nixsolutions.com)
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
}
