<?php
/**
 * @see Core_Db_Table_Abstract
 */
require_once 'Core/Db/Table/Abstract.php';

/**
 * Model_Menu_Table
 *
 * @category    Application
 * @package     Model_Menu
 * @subpackage  Table
 *
 * @author      Valeriu Baleyko <baleyko.v.v@gmail.com>
 * @copyright   Copyright (c) 2010 NIX Solutions (http://www.nixsolutions.com)
 */
class Menu_Model_Menu_Table extends Core_Db_Table_Abstract
{
    /** Table name */
    protected $_name    = 'menu';

    /** Primary Key */
    protected $_primary = 'id';

    /** Row Class */
    protected $_rowClass = 'Menu_Model_Menu';
}