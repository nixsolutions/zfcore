<?php
/**
 * IndexController for Blog module
 *
 * @category   Application
 * @package    Blog
 * @subpackage Controller
 *
 * @version  $Id: PostController.php 164 2010-07-19 14:01:34Z dmitriy.britan $
 */
class Blog_PostController extends Core_Controller_Action
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
        $postId = $this->_getParam('id', null);
        $post = new Blog_Model_Post_Manager();
        $postContent = $post->getPost($postId);
        if ($postContent) {
            $form = new Blog_Model_Comment_Form_Create();
            $auth = Zend_Auth::getInstance();
            /** update count view */
            $post->incrementCountView($postId);
            /** add comment */
            if ($this->getRequest()->isPost()
                && $form->isValid($this->getRequest()->getPost())
                && $auth->hasIdentity()
            ) {
                $comment  = new Blog_Model_Comment();
                $identity = $auth->getIdentity();
                $values   = $form->getValues();
                $comment->setFromArray(
                    array(
                        'cmt_text' => $values['comment'],
                        'post_id'  => $postId,
                        'user_id'  => $identity->id
                    )
                );
                $comment->save();
                $form = new Blog_Model_Comment_Form_Create();
            }
            $coManager = new Blog_Model_Comment_Manager();
            $this->view->comments = $coManager->getComments($postId);
            $this->view->form = $form;
            $this->view->post = $postContent;
        } else {
            $this->_redirect('/' . $this->_module);
        }
    }

    public function createAction()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->_redirect('/' . $this->_module);
        }
        $form = new Blog_Model_Post_Form_Create();
        $identity = $auth->getIdentity();
        if ($this->getRequest()->isPost()
            && $form->isValid($this->getRequest()->getPost())
        ) {
            $values = $form->getValues();

            $post = new Blog_Model_Post();
            $post->setFromArray(
                array(
                    'post_title' => $values['title'],
                    'post_text'  => $values['text'],
                    'ctg_id'     => $values['category'],
                    'user_id'    => $identity->id,
                    'post_status'=> $values['status']
                )
            );

            $post->save();

            $lastId = $post->getTable()->getAdapter()->lastInsertId();
            $this->_flashMessenger->addMessage('Post created');
            $this->_redirect(
                Zend_View_Helper_Url::url(
                    array('action' => 'index', 'id' => $lastId)
                )
            );
        }
        $this->view->form = $form;
    }

    public function editAction()
    {

        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->_flashMessenger->addMessage('You are not authorizated');
            $this->_redirect('/' . $this->_module);
        }

        $identity = $auth->getIdentity();
        $postId = $this->_getParam('id', null, 'integer');
        $post = new Blog_Model_Post_Manager();
        $postContent = $post->getPost($postId);

        if ($postContent->user_id != $identity->id) {
            $this->_flashMessenger->addMessage('You are not creator of this post');
            $this->_redirect('/' . $this->_module);
        }

        $form = new Blog_Model_Post_Form_Edit();
        $form->setValues($postContent->toArray());
        if ($this->getRequest()->isPost()
            && $form->isValid($this->getRequest()->getPost())
        ) {
            $values = $form->getValues();
            $post->updatePost($postId, $values);
            $this->_flashMessenger->addMessage('Post saved');
            $this->_redirect(
                Zend_View_Helper_Url::url(
                    array('controller' => 'post', 'action' => 'index', 'id' => $postId)
                )
            );
        }
        $this->view->form = $form;
    }

}