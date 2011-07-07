<?php
/**
 * Blog_CommentsController for Blog module
 *
 * @category   Application
 * @package    Blog
 * @subpackage Controller
 *
 * @version  $Id: PostController.php 164 2010-07-19 14:01:34Z dmitriy.britan $
 */
class Blog_CommentController extends Core_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
        parent::init();
        $this->_module = $this->getRequest()->getModuleName();
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
    }

    public function indexAction()
    {
        if (!$postId = $this->_getParam('postId')) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }

        $coManager = new Blog_Model_Comment_Manager();
        $this->view->comments = $coManager->getComments($postId);

        $this->view->form = new Blog_Model_Comment_Form_Create();

        if (!$postId = $this->_getParam('postId')) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }
        $form = new Blog_Model_Comment_Form_Create();

        if ($this->getRequest()->isPost()
        && $form->isValid($this->_getAllParams())) {

            $table = new Blog_Model_Comment_Table();
            $row = $table->createRow($form->getValues());
            $row->postId = $postId;
            $row->save();

            $this->_flashMessenger->addMessage('Comment added successfully');
            $this->_helper->redirector(
                        'index',
                        'post',
                        'blog',
            array('id' => $postId)
            );
        }
        $this->view->form = $form;
    }
}