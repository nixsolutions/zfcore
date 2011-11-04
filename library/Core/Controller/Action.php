<?php
/**
 * Class Core_Controller_Action
 *
 * Controller class for our application
 *
 * @category Core
 * @package  Core_Controller
 * @uses     Core_Controller_Action
 */
abstract class Core_Controller_Action extends Zend_Controller_Action
{
    /**
     * before stack
     *
     * @var array
     */
    protected $_before = array();

    /**
     * after stack
     *
     * @var array
     */
    protected $_after = array();

    /**
     * _isDashboard
     *
     * set required options for Dashboard controllers
     *
     * @return  Core_Controller_Action
     */
    protected function _isDashboard()
    {
        // change layout
        $this->_helper->layout->setLayout('dashboard/layout');

        return $this;
    }

    /**
     * Init dojo for current page only
     *
     * @return Core_Controller_Action
     */
    protected function _initDojo()
    {
        // init Dojo Toolkit
        $this->view->addHelperPath('Zend/Dojo/View/Helper/', 'Zend_Dojo_View_Helper');

        // setup dojo
        $this->view->dojo()
            ->enable()
            ->setCdnBase(Zend_Dojo::CDN_BASE_GOOGLE)
            ->setCdnDojoPath(Zend_Dojo::CDN_DOJO_PATH_GOOGLE)
            ->registerModulePath('core', $this->view->baseUrl('/scripts/core'))
            ->setCdnVersion('1.6.0')
            ->requireModule('dojo.parser')
            ->requireModule('dojo.fx')
            ->requireModule('dojo.data.ItemFileReadStore')
            ->requireModule('dijit.form.FilteringSelect')
            /** use dojo theme tundra */
            ->addStyleSheetModule('dijit.themes.tundra')
            ->setDjConfig(array('isDebug' => false, 'parseOnLoad' => true, 'baseUrl' => './'));

        $this->view->initDojo = true;

        return $this;
    }


    /**
     * add function to stack
     *
     * @param $function
     * @param array $options
     * @return void
     */
    protected function _before($function, $options = array())
    {
        $this->_before[] = array(
            'function' => $function,
            'options' => $options
        );
    }

    /**
     * add function to stack
     *
     * @param $function
     * @param array $options
     * @return void
     */
    protected function _after($function, $options = array())
    {
        $this->_after[] = array(
            'function' => $function,
            'options' => $options
        );
    }

    /**
     * init before filter
     *
     * @return void
     */
    protected function _initBefore()
    {
        $action = $this->getRequest()->getActionName();
        $this->_execFunctions($this->_before, $action);
    }

    /**
     * init after filter
     *
     * @return void
     */
    protected function _initAfter()
    {
        $action = $this->getRequest()->getActionName();
        $this->_execFunctions($this->_after, $action);
    }

    /**
     * execute functions in stack
     *
     * @param $stack
     * @param $action
     * @return bool
     */
    protected function _execFunctions($stack, $action)
    {
        foreach ($stack as $item) {

            /** check if function is only for current action */
            if (!empty($item['options']['only'])) {
                $only = (array) $item['options']['only'];
                if (!in_array($action, $only)) {
                    continue;
                }
            }

            /** check if function is skipped for current action */
            if (!empty($item['options']['skip'])) {
                $skip = (array) $item['options']['skip'];
                if (in_array($action, $skip)) {
                    continue;
                }
            }

            /** execute */
            $functions = (array) $item['function'];
            foreach ($functions as $function) {

                /** next functions won't be executed if current one returns false */
                if (false === $this->$function()) {
                    return false;
                }
            }
        }
    }

    /**
     * clear before filter
     *
     * @return void
     */
    protected function _clearBefore()
    {
        $this->_before = array();
    }

    /**
     * clear after filter
     *
     * @return void
     */
    protected function _clearAfter()
    {
        $this->_after = array();
    }

    /**
     * pre-dispatch routines
     *
     * @return void
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $this->_initBefore();
    }

    /**
     * post-dispatch routines
     *
     * @return void
     */
    public function postDispatch()
    {
        parent::postDispatch();
        $this->_initAfter();
    }

    /**
     * forward to not found page
     *
     * @return void
     */
    protected function _forwardNotFound()
    {
        $this->_forward('notfound', 'error', 'default');
    }
}
