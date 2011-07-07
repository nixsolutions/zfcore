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
 * @version  $Id: Manager.php 163 2010-07-12 16:30:02Z AntonShevchuk $
 */
class Blog_Model_Comment_Manager extends Core_Model_Manager
{
    /**
     * get comments for some post
     *
     * @param integer $postId
     * @return array
     */
    public function getComments($postId)
    {
        $select = $this->getDbTable()->select()->setIntegrityCheck(false)
                ->from(array('c' => 'blog_comment'), array('*'))
                ->joinLeft(
                    array('u' => 'users'),
                    'c.userId = u.id',
                    array('login')
                )
                ->where('c.postId = ?', $postId);
        return $this->getDbTable()->fetchAll($select);
    }
}