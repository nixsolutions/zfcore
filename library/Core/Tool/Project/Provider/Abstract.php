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

require_once 'Zend/Tool/Project/Provider/Abstract.php';

/**
 * Abstract Core Provider
 *
 * @category Core
 * @package  Core_Tool
 *
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 */
abstract class Core_Tool_Project_Provider_Abstract
    extends Zend_Tool_Project_Provider_Abstract
{
    /**
     * constructor
     */
    public function initialize()
    {
        parent::initialize();

        return;
        // load Core Context
        $contextRegistry = Zend_Tool_Project_Context_Repository::getInstance();
        $contextRegistry->addContextsFromDirectory(
            dirname(dirname(__FILE__)) . '/Context/Core/',
            'Core_Tool_Project_Context_Core_'
        );
    }

    /**
     * Method returns path to project directory
     *
     * @param  Zend_Tool_Project_Profile $profile
     * @return string
     */
    protected static function _getProjectDirectoryPath(
        Zend_Tool_Project_Profile $profile
    )
    {
        $projectDirectory = $profile->search(array('projectDirectory'));

        if (!($projectDirectory instanceof Zend_Tool_Project_Profile_Resource)) {
            throw new Zend_Tool_Project_Provider_Exception(
                "Project resource undefined."
            );
        }

        return $projectDirectory->getPath();
    }

    /**
     * Method returns path to modules directory
     *
     * @param  Zend_Tool_Project_Profile $profile
     * @return string
     */
    protected static function _getModulesDirectoryPath(
        Zend_Tool_Project_Profile $profile
    )
    {
        $modulesDirectory = $profile->search(array('modulesDirectory'));

        if (!($modulesDirectory instanceof Zend_Tool_Project_Profile_Resource)) {
            throw new Zend_Tool_Project_Provider_Exception(
                "Modules resource undefined."
            );
        }

        return $modulesDirectory->getPath();
    }

}
