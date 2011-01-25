<?php
/**
 * This is the Manager class for the pages table.
 *
 * @category Application
 * @package Model
 * @subpackage Manager
 * 
 * @version  $Id: Manager.php 47 2010-02-12 13:17:34Z AntonShevchuk $
 */
class Pages_Model_Page_Table extends Core_Db_Table_Abstract
{
    /** Table name */
    protected $_name = 'pages';

    /** Primary Key */
    protected $_primary = 'id';
    
    /** Row Class */
    protected $_rowClass = 'Pages_Model_Page';
}