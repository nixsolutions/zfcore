<?php
/**
 * IndexController for forum module
 *
 * @category   Application
 * @package    Forum
 * @subpackage Controller
 *
 * @version  $Id: IndexController.php 146 2010-07-05 14:22:20Z AntonShevchuk $
 */
class Forum_IndexController extends Core_Controller_Action
{
    /**
     * Index
     */
    public function indexAction()
    {
        $ctg = new Forum_Model_Category_Manager();
        $post = new Forum_Model_Post_Manager();

        $source = $post->getPostsSourse($this->_getParam('id', 0));
        $paginator = Zend_Paginator::factory($source);

        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($this->_getParam('page'));

        $this->view->cats = $ctg->getChildren();

        $ids = array();
        foreach ($ctg->getChildren()as $cat) {
            $ids[] = $cat->id;
        }

        $this->view->catsInfo = $post->getInfoByCategories($ids);
        $this->view->paginator = $paginator;
    }

}
