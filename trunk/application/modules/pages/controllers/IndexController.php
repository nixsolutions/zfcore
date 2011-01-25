<?php
/**
 * PagesController for default module
 *
 * @category   Application
 * @package    Pages
 * @subpackage Controller
 * 
 * @version  $Id: IndexController.php 146 2010-07-05 14:22:20Z AntonShevchuk $
 */
class Pages_IndexController extends Core_Controller_Action
{
    /**
     * Init Controller
     */
    public function init()
    {
        parent::init();
        $this->_helper->contextSwitch
                      ->setActionContext('sitemapxml', 'xml')
                      ->initContext('xml');
    }
    /**
     * indexAction
     *
     * Index action in Pages Controller
     *
     * @class Static
     * @access public
     * @created Wed Aug 06 13:09:14 EEST 2008
     */
    public function indexAction()
    {
        $pageTable = new Pages_Model_Page_Table();
        
        if (is_null($this->_getParam('alias'))
            or !($page = $pageTable->getByAlias($this->_getParam('alias')))) {
            return $this->_forward('notfound', 'error', 'default');
        }
        
        $this->view->Page = $page;
    }
    
    /**
     * Sitemap
     *
     */
    public function sitemapxmlAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo $this->view->navigation()->sitemap()
                                      ->setFormatOutput(true)
                                      ->setUseSitemapValidators(false);
    }
    
    /**
     * Sitemap
     *
     */
    public function sitemapAction()
    {
        
    }
}

