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

        // init Dojo Toolkit
        $this->view->addHelperPath('Zend/Dojo/View/Helper/', 'Zend_Dojo_View_Helper');

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
     * init before filter
     *
     * @return bool
     */
    protected function _initBefore()
    {
        $action = $this->getRequest()->getActionName();

        foreach ($this->_before as $item) {

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
     * forward to not found page
     *
     * @return void
     */
    protected function _forwardNotFound()
    {
        $this->_forward('notfound', 'error', 'default');
    }
}
