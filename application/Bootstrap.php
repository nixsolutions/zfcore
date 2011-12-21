<?php
/**
 * Bootstrap Application
 *
 * @category Application
 * @package  Bootstrap
 *
 * @version  $Id: Bootstrap.php 1607 2009-12-02 15:10:38Z dark $
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     *
     * @return Zend_View
     */
    protected function _initView()
    {
        $this->bootstrap('layout');

        $options = $this->getOption('view');

        // Initialize view
        $view = new Core_View();
        $view->headMeta()
             ->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8')
             ->appendHttpEquiv('Content-Language', 'en-US');
        $view->headTitle($options['title']);
        $view->doctype($options['doctype']);

        /**
         * FIXME:
         * <code>
         *   resources.view.helperPath.Core_View_Helper = "Core/View/Helper"
         *   resources.view.filterPath.Core_View_Filter = "Core/View/Filter"
         * </code>
         */
        $view->addHelperPath('Core/View/Helper/', 'Core_View_Helper');
        $view->addFilterPath('Core/View/Filter', 'Core_View_Filter');

        /* Application specified scripts/helpers/filters */
        $view->addScriptPath(APPLICATION_PATH . '/views/scripts');
        $view->addHelperPath(APPLICATION_PATH . '/layouts/helpers', 'Application_View_Helper');
        $view->addFilterPath(APPLICATION_PATH . '/views/filters', 'Application_View_Filter');

        $view->assign('env', APPLICATION_ENV);

        // Add it to the ViewRenderer
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);

        // Return it, so that it can be stored by the bootstrap
        return $view;
    }
}