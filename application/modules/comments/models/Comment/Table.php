<?php
/**
 * This is the table class for the comments table.
 *
 * @category Application
 * @package Comments
 * @subpackage Model
 */
class Comments_Model_Comment_Table extends Core_Db_Table_Abstract
{
    /** Table name */
    protected $_name = 'comments';

    /** Primary Key */
    protected $_primary = 'id';

    /** Row Class */
    protected $_rowClass = 'Comments_Model_Comment';
}