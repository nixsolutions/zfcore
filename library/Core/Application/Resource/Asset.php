<?php
/**
 * Asset Resource
 *
 * @category Core
 * @package  Core_Application
 * @subpackage Resource
 */
class Core_Application_Resource_Asset extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        $asset = new Core_Asset($this->getOptions());

        Zend_Registry::set('Core_Asset', $asset);
        return $asset;
    }
}
