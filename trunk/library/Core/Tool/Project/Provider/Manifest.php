<?php
/**
 * Core Manifest
 * 
 * @category Core
 * @package  Core_Tool
 * 
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 */
class Core_Tool_Project_Provider_Manifest 
    implements Zend_Tool_Framework_Manifest_ProviderManifestable
{
    /**
     * Return list of all providers
     *
     * @return array
     */
    public function getProviders()
    {
        return array(
            new Core_Tool_Project_Provider_MigrationProvider
        );
    }
}