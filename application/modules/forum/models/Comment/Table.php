<?php
/**
 * This is the Manager class for the bf_comment table.
 *
 * @category Application
 * @package Model
 * @subpackage Manager
 * 
 * @author Ivan Nosov aka rewolf <i.k.nosov@gmail.com>
 * 
 * @version  $Id: Manager.php 48 2010-02-12 13:23:39Z AntonShevchuk $
 */
class Forum_Model_Comment_Table extends Core_Db_Table_Abstract
{
    /** Table name */
    protected $_name = 'bf_comment';

    /** Primary Key */
    protected $_primary = 'id';
    
    /** Row Class */
    protected $_rowClass = 'Forum_Model_Comment';    
}