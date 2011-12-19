<?php
/**
 * Concrete class for handling view scripts.
 *
 * @category Core
 * @package  Core_View
 *
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 *
 * @version  $Id: View.php 48 2010-02-12 13:23:39Z AntonShevchuk $
 */
class Core_View extends Zend_View
{
    /**
     * __
     *
     * @param  string $messageid Id of the message to be translated
     * @param  string $module
     * @return string Translated message
     */
    public function __($messageid = null, $module = null)
    {
        if (null === $messageid) {
            return '';
        }
        return $this->translate($messageid, $module);
    }

    /**
     * _e
     *
     * @param  string $messageid Id of the message to be translated
     * @param  string $module
     * @return string Translated message
     */
    public function _e($messageid = null, $module = null)
    {
        echo $this->__($messageid, $module);
    }
}
