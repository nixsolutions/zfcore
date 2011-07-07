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
        if (!$alias = $this->_getParam('alias')) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }
        $post = new Blog_Model_Post_Manager();
        if (!$postContent = $post->getPost($alias)) {
            throw new Zend_Controller_Action_Exception('Post not found');
        }

        /** update count view */
        $post->incrementCountView($postContent->id);
        $this->view->row = $postContent;
    }

    public function createAction()
    {
        $form = new Blog_Model_Post_Form_Create();
        if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
            $post = new Blog_Model_Post();
            $post->setFromArray($this->_getAllParams());
            $post->save();

            $this->_flashMessenger->addMessage('Post created');
            $this->_helper->redirector(
                'index',
                null,
                null,
                array('alias' => $post->alias)
            );
        }
        $this->view->form = $form;
    }

    public function editAction()
    {
        if (!$alias = $this->_getParam('alias')) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }
        $post = new Blog_Model_Post_Manager();
        if (!$postContent = $post->getPost($alias)) {
            throw new Zend_Controller_Action_Exception('Post not found');
        }

        if (!$postContent->isOwner()) {
            throw new Zend_Controller_Action_Exception('Page is forbidden');
        }

        $form = new Blog_Model_Post_Form_Edit();
        $form->setDefaults($postContent->toArray());

        if ($this->getRequest()->isPost()
            && $form->isValid($this->_getAllParams())) {

            $post->updatePost($postId, $form->getValues());

            $this->_flashMessenger->addMessage('Post saved');
            $this->_helper->redirector(
                'index',
                null,
                null,
                array('alias' => $post->alias)
            );
        }
        $this->view->form = $form;
    }

}