<?php
/**
 * This is the DbTable class for the mail table.
 *
 * @category Application
 * @package Model
 * @subpackage Xml
 *
 * @version  $Id: Manager.php 47 2010-02-12 13:17:34Z AntonShevchuk $
 */
class Mail_Model_Templates_Xml
{
    /**
     * @var string
     */
    protected $_path;

    /**
     * Constructor;
     *
     * @param string $path
     * @throws Exception
     */
    public function __construct($path = null)
    {
        if ($path) {
            $this->setPath($path);
        }
    }

    /**
     * Set path;
     *
     * @param string $path
     * @return self
     * @throws Exception
     */
    public function setPath($path)
    {
        $this->_path = realpath($path);
        if (!$this->_path) {
            throw new Exception("Path '{$path}' not exists");
        }
    }

    /**
     * Get template model
     *
     * @param  string $alias
     * @return Mail_Model_Templates_Model
     */
     public function getModel($alias)
     {
         if (file_exists($this->_path . $alias . ".xml")) {
             $content = simplexml_load_file($this->_path . $alias .'.xml');
             $data = get_object_vars($content);
         } else {
             return array();
         }
         return new Mail_Model_Templates_Model($data);
     }
}
