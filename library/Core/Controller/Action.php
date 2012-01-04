<?php
/**
 * Copyright (c) 2012 by PHP Team of NIX Solutions Ltd
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * Class Core_Controller_Action
 *
 * Controller class for our application
 *
 * @category Core
 * @package  Core_Controller
 *
 * @uses     Zend_Controller_Action
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
        $this->_helper->layout->setLayout( 'dashboard/layout' );

        return $this;
    }

    /**
     * add function to stack
     *
     * @param       $function
     * @param array $options
     * @return void
     */
    protected function _before($function, $options = array())
    {
        $this->_before[] = array(
            'function' => $function,
            'options'  => $options
        );
    }

    /**
     * add function to stack
     *
     * @param       $function
     * @param array $options
     * @return void
     */
    protected function _after($function, $options = array())
    {
        $this->_after[] = array(
            'function' => $function,
            'options'  => $options
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
        $this->_execFunctions( $this->_before, $action );
    }

    /**
     * init after filter
     *
     * @return void
     */
    protected function _initAfter()
    {
        $action = $this->getRequest()->getActionName();
        $this->_execFunctions( $this->_after, $action );
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
                $only = (array)$item['options']['only'];
                if (!in_array( $action, $only )) {
                    continue;
                }
            }

            /** check if function is skipped for current action */
            if (!empty($item['options']['skip'])) {
                $skip = (array)$item['options']['skip'];
                if (in_array( $action, $skip )) {
                    continue;
                }
            }

            /** execute */
            $functions = (array)$item['function'];
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
        $this->_forward( 'notfound', 'error', 'users' );
    }
}
