<?php
/**
 * This is the DbTable class for the pages table.
 *
 * @category Application
 * @package Model
 * @subpackage DbTable
 * 
 * @version  $Id: Manager.php 47 2010-02-12 13:17:34Z AntonShevchuk $
 */
class Model_User_Table extends Core_Db_Table_Abstract
{
    /** Table name */
    protected $_name    = 'users';
    
    /** Primary Key */
    protected $_primary = 'id';
    
    /** Row Class */
    protected $_rowClass = 'Model_User';
}