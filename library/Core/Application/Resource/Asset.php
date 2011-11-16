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
        $options = $this->getOptions();

        $assets = array();
        foreach ($options['packages'] as $package => $config) {

            /** use parent adapter options if package ones are empty */
            if (empty($config['adapter']) &&
                !empty($options['adapter'])
            ) {
                $config['adapter'] = $options['adapter'];
            }

            /** extend */
            if (!empty($config['extend'])) {
                $config['extend'] = (array) $config['extend'];
                foreach ($config['extend'] as &$extend) {
                    if (empty($assets[$extend])) {
                        throw new Core_Exception('Package "' . $extend . '" not found');
                    }

                    $extend = $assets[$extend];
                }
            }

            $assets[$package] = new Core_Asset($config);
        }

        Zend_Registry::set('assets', $assets);
        return $assets;
    }
}
