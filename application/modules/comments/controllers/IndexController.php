<?php
/**
 * Comments_IndexController for Comments module
 *
 * @category   Application
 * @package    Comments
 * @subpackage Controller
 *
 * @version  $Id: SubscribeController.php 2011-11-21 11:59:34Z pavel.machekhin $
 */
class Comments_IndexController extends Core_Controller_Action
{
    public function indexAction()
    {
        $postId = (int) $this->_getParam('postId', 0);
        $categoryAlias = $this->_getParam('categoryAlias');
        
        if (!$postId || !$categoryAlias) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }
        
        $manager = new Comments_Model_Comment_Manager();
        $this->view->rowset = $manager->findAll($postId, $categoryAlias);
        $this->view->form = new Comments_Model_Comment_Form_Create();
        
        $this->view->form->setUser($this->view->user());
    }
}