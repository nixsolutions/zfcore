<?php
/**
 * @see Zend_Config
 */
require_once 'Zend/Config.php';

/**
 * YAML Adapter for Zend_Config
 *
 * @category  Zend
 * @package   Zend_Config
 * @copyright Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Core_Config_Yaml extends Zend_Config_Yaml
{
    /**
     * Get Only user defined constants
     *
     * @return array
     */
    protected static function _getConstants()
    {

        $constants = get_defined_constants(true);
        $constants = array_keys($constants['user']);

        rsort($constants, SORT_STRING);
        return $constants;
    }
}
