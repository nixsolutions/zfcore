<?php
/**
 * This is the Manager class for the blog_comment table.
 *
 * @category Application
 * @package Model
 * @subpackage Manager
 *
 * @author Ivan Nosov aka rewolf <i.k.nosov@gmail.com>
 *
 * @version  $Id: Manager.php 48 2010-02-12 13:23:39Z AntonShevchuk $
 */
class Blog_Model_Comment_Table extends Core_Db_Table_Abstract
{
    /** Table name */
    protected $_name = 'blog_comment';

    /** Primary Key */
    protected $_primary = 'id';

    /** Row Class */
    protected $_rowClass = 'Blog_Model_Comment';
}