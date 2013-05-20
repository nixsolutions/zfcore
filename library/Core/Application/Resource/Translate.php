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
 * @category   Core
 * @package    Core_Application
 * @subpackage Resource
 */
class Core_Application_Resource_Translate
    extends Zend_Application_Resource_Translate
{
    /**
     * Init Resource
     * @return \Zend_Translate
     */
    public function init()
    {
        //return;
        if (!isset($this->_options['content'], $this->_options['data'])) {

            $this->getBootstrap()->bootstrap('Modules');

            $this->_options['content'] = Translate_Model_Translate::getTranslationPath();
            $this->_options['adapter'] = Translate_Model_Translate::ADAPTER;
        }

        $log = $this->getBootstrap()->bootstrap('frontController')->getResource('log');
        if (isset($this->_options['logUntranslated']) && $log) {
            $this->_options['log'] = $log;
        }

        $translate = $this->getTranslate();
        $front = $this->getBootstrap()->bootstrap('frontController')
            ->getResource('frontController');


        $front->registerPlugin(new Core_Controller_Plugin_Translate($translate));

        return $translate;
    }
}