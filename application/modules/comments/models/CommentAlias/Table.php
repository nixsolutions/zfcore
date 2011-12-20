<?php
/**
 * This is the table class for the comment_aliases table.
 *
 * @category Application
 * @package Comments
 * @subpackage Model
 *
 * @version  $Id: Comment.php 2011-11-21 11:59:34Z pavel.machekhin $
 */
class Comments_Model_CommentAlias_Table extends Core_Db_Table_Abstract
{
    /** Table name */
    protected $_name = 'comment_aliases';

    /** Primary Key */
    protected $_primary = 'id';

    /** Row Class */
    protected $_rowClass = 'Comments_Model_CommentAlias';
}