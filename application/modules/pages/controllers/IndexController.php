<?php
/**
 * PagesController
 *
 * @category   Application
 * @package    Pages
 * @subpackage Controller
 */
class Pages_IndexController extends Core_Controller_Action
{
    /**
     * Init Controller
     *
     * @return void
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
     * @return void
     */
    public function indexAction()
    {
        if (!$alias = $this->_getParam('alias')) {
            $this->_forwardNotFound();
        }

        $table = new Pages_Model_Page_Table();

        if (!$page = $table->getByAlias($alias)) {
            $this->_forwardNotFound();
        }
        $this->view->page = $page;
    }

    /**
     * Sitemap
     *
     * @return void
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
     * @return void
     */
    public function sitemapAction()
    {
        
    }
}
