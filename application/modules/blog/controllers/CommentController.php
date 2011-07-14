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
    public function indexAction()
    {
        if (!$postId = $this->_getParam('postId')) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }
        $posts = new Blog_Model_Post_Table();
        if (!$post = $posts->getById($postId)) {
            throw new Zend_Controller_Action_Exception('Post not found');
        }

        $table = new Blog_Model_Comment_Table();
        $this->view->rowset = $table->getByPost($postId);

        $form = new Blog_Model_Comment_Form_Create();

        if (Zend_Auth::getInstance()->hasIdentity()) {
            if ($this->getRequest()->isPost()
                && $form->isValid($this->_getAllParams())) {

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