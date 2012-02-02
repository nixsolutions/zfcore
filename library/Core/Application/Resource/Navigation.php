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
 *
 * @uses       Zend_Application_Resource_ResourceAbstract
 * @category   Core
 * @package    Core_Application
 * @subpackage Resource
 *
 * @author     Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link       http://anton.shevchuk.name
 */
class Core_Application_Resource_Navigation extends Zend_Application_Resource_ResourceAbstract
{

    /**
     * configuration file with settings
     *
     * @var string
     */
    protected $_config = 'navigation';

    /**
     * cache using
     *
     * @var string
     */
    protected $_cache;

    /**
     * Constructor
     */
    public function init()
    {

        $options = $this->getOptions();
        if (isset($options['cache'])) {
            $this->_cache = $options['cache'];
        }
        if (isset($options['config'])) {
            $this->_config = $options['config'];
        }

        $this->_initNavigation();
    }

    /**
     * _initNavigation
     *
     * @return  void
     */
    protected function _initNavigation()
    {
        $menu = $this->_getConfig();
        Zend_Registry::set('menuArray', $menu);
    }

    /**
     * get config
     *
     * @return array
     */
    protected function _getConfig()
    {
        return Core_Module_Config::getConfig(
            $this->_config,
            null,
            Core_Module_Config::MAIN_ORDER_FIRST,
            $this->_cache
        );
    }
}