<?php

require_once 'Zend/Tool/Project/Context/Filesystem/File.php';

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
