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
    }

    /**
     * View post
     *
     * @throws Zend_Controller_Action_Exception
     */
    public function indexAction()
    {
        if (!$alias = $this->_getParam('alias')) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }
        $posts = new Blog_Model_Post_Table();
        if (!$row = $posts->getByAlias($alias)) {
            throw new Zend_Controller_Action_Exception('Post not found');
        }
        $users = new Users_Model_Users_Table();
        $this->view->user = $users->getById($row->userId);

        $categories = new Categories_Model_Category_Table();
        $this->view->category = $categories->getById($row->categoryId);


        /** update count view */
        $row->incViews();
        $this->view->row = $row;
        $this->view->page = $this->_getParam('page');
    }

    /**
     * Create new post
     */
    public function createAction()
    {
        $form = new Blog_Model_Post_Form_Create();
        if ($this->getRequest()->isPost()
            && $form->isValid($this->_getAllParams())) {

            $posts = new Blog_Model_Post_Table();
            $post = $posts->createRow();
            $post->setFromArray($form->getValues());
            $post->save();

            $this->_helper->flashMessenger->addMessage('Post created');
            $this->_helper->redirector(
                'index',
                null,
                null,
                array('alias' => $post->alias)
            );
        }
        $this->view->form = $form;
    }

    /**
     * Edit my post
     *
     * @throws Zend_Controller_Action_Exception
     */
    public function editAction()
    {
        if (!$alias = $this->_getParam('alias')) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }
        $posts = new Blog_Model_Post_Table();
        if (!$post = $posts->getByAlias($alias)) {
            throw new Zend_Controller_Action_Exception('Post not found');
        }

        if (!$post->isOwner()) {
            throw new Zend_Controller_Action_Exception('Page is forbidden');
        }

        $form = new Blog_Model_Post_Form_Edit();
        $form->setDefaults($post->toArray());

        if ($this->getRequest()->isPost()
            && $form->isValid($this->_getAllParams())) {

            $post->setFromArray($form->getValues());

            $this->_helper->flashMessenger->addMessage('Post saved');
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