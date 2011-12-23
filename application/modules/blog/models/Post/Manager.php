<?php
/**
 * Post DBTable
 *
 * @category Application
 * @package Model
 * @subpackage Post
 *
 * @version  $Id: Manager.php 162 2010-07-12 14:58:58Z AntonShevchuk $
 */
class Blog_Model_Post_Manager extends Core_Model_Manager
{
    public function getSelect()
    {
        $select = $this->getDbTable()->select();
        
        return $select;
    }
}