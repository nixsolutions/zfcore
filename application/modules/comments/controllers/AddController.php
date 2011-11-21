<?php
/**
 * Comments_AddController for Comments module
 *
 * @category   Application
 * @package    Blog
 * @subpackage Controller
 *
 * @version  $Id: AddController.php 2011-11-21 11:59:34Z pavel.machekhin $
 */
class Comments_AddController extends Core_Controller_Action
{
    public function indexAction()
    {
        if (!$postId = $this->_getParam('postId')) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }
        $posts = new Blog_Model_Post_Table();
        if (!$post = $posts->getById($postId)) {
            throw new Zend_Controller_Action_Exception('Post not found');
        }

        if (Zend_Auth::getInstance()->hasIdentity()) {
            $form = new Blog_Model_Comment_Form_Create();
            
            if ($this->getRequest()->isPost()
                && $form->isValid($this->_getAllParams())
            ) {

                $row = $table->createRow($form->getValues());
                $row->postId = $postId;
                $row->save();

                $this->_helper->flashMessenger->addMessage(
                    'Comment added successfully'
                );
                $this->_helper->redirector(
                    'index',
                    'post',
                    'blog',
                    array('alias' => $post->alias)
                );
            }
            $this->view->form = $form;
        }
    }
}