<?php
/**
 * Asset Resource
 *
 * @category Core
 * @package  Core_Application
 * @subpackage Resource
 *
 * <code>
 * configuration
 *
 * using simple adapter
 * resources:
 *   asset:
 *     dir: PUBLIC_PATH/assets/
 *     buildDir: PUBLIC_PATH/assets/builds/
 *     adapter: Core_Asset_Adapter_Simple
 *
 * using yui compressor adapter
 * resources:
 *   asset:
 *     dir: PUBLIC_PATH/assets/
 *     buildDir: PUBLIC_PATH/assets/builds/
 *     adapter:
 *       class: Core_Asset_Adapter_YuiCompressor
 *       jarPath: APPLICATION_PATH/../data/yuicompressor-2.4.6.jar
 * </code>
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
