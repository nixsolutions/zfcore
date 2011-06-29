<?php
/**
 * Model Post
 *
 * @category Application
 * @package Model
 *
 * @version  $Id: Post.php 146 2010-07-05 14:22:20Z AntonShevchuk $
 */
class Blog_Model_Post extends Core_Db_Table_Row_Abstract
{
    /** statuses */
    const STATUS_ACTIVE  = 'active';
    const STATUS_CLOSED  = 'closed';
    const STATUS_DELETED = 'deleted';
}