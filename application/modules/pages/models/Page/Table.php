<?php
/**
 * This is the Manager class for the pages table.
 *
 * @category Application
 * @package Model
 * @subpackage Manager
 * @method getByAlias
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