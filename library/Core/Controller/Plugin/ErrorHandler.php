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
 * Controller plugin that sets the correct paths
 * to the Zend_Controller_Plugin_errorHandler instances
 *
 * @uses       Zend_Controller_Plugin_Abstract
 *
 * @category   Core
 * @package    Core_Controller
 * @subpackage Plugin
 *
 * @author     Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link       http://anton.shevchuk.name
 */
class Core_Controller_Plugin_ErrorHandler
    extends Zend_Controller_Plugin_Abstract
{
    /**
     * dispatchLoopStartup
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        // Configure the error plugin to use the loaded module
        // so we can use module-specific error handling
        $frontController = Zend_Controller_Front::getInstance();
        $errorPlugin = $frontController->getPlugin( 'Zend_Controller_Plugin_ErrorHandler' );
        /* @var Zend_Controller_Plugin_ErrorHandler $errorPlugin */
        $errorPlugin->setErrorHandlerModule( $request->getModuleName() );
    }
}