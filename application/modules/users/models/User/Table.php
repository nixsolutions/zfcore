<?php
/**
 * This is the DbTable class for the users table.
 *
 * @category Application
 * @package Model
 * @subpackage DbTable
 */
class Users_Model_User_Table extends Core_Db_Table_Abstract
{
    /** Table name */
    protected $_name    = 'users';
    
    /** Primary Key */
    protected $_primary = 'id';
    
    /** Row Class */
    protected $_rowClass = 'Users_Model_User';
}