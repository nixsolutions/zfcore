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

require_once 'Zend/Tool/Project/Context/Filesystem/File.php';

/**
 * @category   Core
 * @package    Core_Tool
 */
class Core_Tool_Project_Context_Core_MigrationFile
    extends Zend_Tool_Project_Context_Filesystem_File
{
    /**
     * @var string
     */
    protected $_migrationName = null;

    /**
     * @var string
     */
    protected $_filesystemName = null;

    /**
     * getName()
     *
     * @return string
     */
    public function getName()
    {
        return 'MigrationFile';
    }

    public function getMigrationName()
    {
        return $this->_migrationName;
    }

    public function init()
    {
        if (null === $this->_migrationName) {
            list($sec, $msec) = explode(".", microtime(true));
            $this->_migrationName = date('Ymd_His_') . sprintf("%02d", $msec);
        }

        $this->_filesystemName = $this->_migrationName . '.php';

        parent::init();
    }

    public function getContents()
    {
        // Configuring after instantiation
        $methodUp = new Zend_CodeGenerator_Php_Method();
        $methodUp->setName('up')
            ->setBody('// upgrade');

        // Configuring after instantiation
        $methodDown = new Zend_CodeGenerator_Php_Method();
        $methodDown->setName('down')
            ->setBody('// degrade');


        $class = new Zend_CodeGenerator_Php_Class();
        $class->setName('Migration_' . $this->getMigrationName())
            ->setExtendedClass('Core_Migration_Abstract')
            ->setMethod($methodUp)
            ->setMethod($methodDown);

        $file = new Zend_CodeGenerator_Php_File();
        $file->setClass($class)
            ->setFilename($this->getPath());

        return $file->generate();
    }
}
