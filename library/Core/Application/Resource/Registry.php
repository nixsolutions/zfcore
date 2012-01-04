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
 * Registry Resource
 *
 * <code>
 * ; example of application.ini
 * ; init registry
 * resources.registry = true
 * ; or set some variables with autoinit
 * resources.registry.var = "value"
 * </code>
 *
 * <code>
 * // get application config in your application
 * Zend_Registry::get('Application_Config');
 * </code>
 *
 * @category   Core
 * @package    Core_Application
 * @subpackage Resource
 *
 * @author     Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link       http://anton.shevchuk.name
 */
class Core_Application_Resource_Registry
    extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Init Resource
     */
    public function init()
    {
        $registry = Zend_Registry::getInstance();

        // set custom
        foreach ((array)$this->getOptions() as $key => $value) {
            $registry->set( $key, $value );
        }
    }
}