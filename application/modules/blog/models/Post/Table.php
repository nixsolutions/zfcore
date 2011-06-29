<?php
/**
 * Post DBTable
 *
 * @category Application
 * @package Model
 * @subpackage Post
 *
 * @version  $Id: Manager.php 48 2010-02-12 13:23:39Z AntonShevchuk $
 */
class Blog_Model_Post_Table extends Core_Db_Table_Abstract
{
    /** Table name */
    protected $_name = 'blog_post';

    /** Primary Key */
    protected $_primary = 'id';

    /** Row Class */
    protected $_rowClass = 'Blog_Model_Post';
}