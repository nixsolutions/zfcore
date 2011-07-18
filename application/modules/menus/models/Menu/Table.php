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
class Menus_Model_Menu_Table extends Core_Db_Table_Abstract
{
    /** Table name */
    protected $_name    = 'menu';

    /** Primary Key */
    protected $_primary = 'id';

    /** Row Class */
    protected $_rowClass = 'Menus_Model_Menu';


	/**
     * Get country by ip
     *
     * @param string|int $ip
     * @return array
     */
    public function getMenuItems()
    {
        $select = $this->select()->setIntegrityCheck(false);
		$select->from(array('m' => 'menu'),
					array('m.*'))
               ->joinleft(array('m2' => 'menu'),
                   'm.parent_id = m2.id',
                   array('m2.label AS parent')
               )
               ->order(array('m.id ASC'))
		       /*->limit($limit, $offset)*/;
        $result = $this->fetchAll($select);
        return $result;
    }
}