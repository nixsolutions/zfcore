<?php
/**
 * IndexController for forum module
 *
 * @category   Application
 * @package    Forum
 * @subpackage Controller
 *
 * @version  $Id: PostController.php 164 2010-07-19 14:01:34Z dmitriy.britan $
 */
class Forum_PostController extends Core_Controller_Action
{
    /**
     * View post
     *
     * @throws Zend_Controller_Action_Exception
     */
    public function indexAction()
    {
        if (!$postId = $this->_getParam('id')) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }
        $posts = new Forum_Model_Post_Table();
        if (!$post = $posts->getById($postId)) {
            throw new Zend_Controller_Action_Exception('Post not found');
        }

        $users = new Users_Model_Users_Table();
        $this->view->author = $users->getById($post->userId);

        $categories = new Categories_Model_Category_Table();
        $this->view->category = $categories->getById($post->categoryId);


        /** update count view */
        $post->incViews();
        $this->view->post = $post;
    }

    /**
     * Category
     */
    public function listAction()
    {
        if (!$categoryAlias = $this->_getParam('alias')) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }

        $manager = new Forum_Model_Category_Manager();
        if (!$category = $manager->getByAlias($alias)) {
            throw new Zend_Controller_Action_Exception('Category not found');
        }

        $posts = new Forum_Model_Post_Table();

        $this->view->posts = $posts->getLastPosts();

        $select = $posts->getPostsSelect($category->id);
        $paginator = Zend_Paginator::factory($select);

        $paginator->setItemCountPerPage(20);
        $paginator->setCurrentPageNumber($this->_getParam('page'));

        $this->view->paginator = $paginator;
    }

    /**
     * Create
     *
     */
    public function createAction()
    {
        $form = new Forum_Model_Post_Form_Create();
        if ($this->getRequest()->isPost()
            && $form->isValid($this->_getAllParams())) {

            $post = new Forum_Model_Post();
            $post->setFromArray($form->getValues());
            $post->save();

            $this->_helper->flashMessenger->addMessage('Post created');

            $this->_helper->redirector
                 ->gotoRoute(array('id' => $post->id), 'forumpost');
        }
        $this->view->form = $form;
    }

    /**
     * Edit post
     *
     * @throws Zend_Controller_Action_Exception
     */
    public function editAction()
    {
        if (!$postId = $this->_getParam('id')) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }
        $posts = new Forum_Model_Post_Table();
        if (!$post = $posts->getById($postId)) {
            throw new Zend_Controller_Action_Exception('Post not found');
        }
        if (!$post->isOwner()) {
            throw new Zend_Controller_Action_Exception('Page is forbidden');
        }

        $form = new Forum_Model_Post_Form_Edit();
        $form->populate($post->toArray());

        if ($this->getRequest()->isPost()
            && $form->isValid($this->_getAllParams())) {

            $post->setFromArray($form->getValues());
            $this->_helper->flashMessenger->addMessage('Post saved');

            $this->_helper->redirector
                 ->gotoRoute(array('id' => $post->id), 'forumpost');
        }
        $this->view->form = $form;
    }

}