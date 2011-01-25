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
     * @return string Translated message
     */
    public function __($messageid = null)
    {
        return $this->translate($messageid);
    }

    /**
     * _e
     *
     * @param  string $messageid Id of the message to be translated
     * @return string Translated message
     */
    public function _e($messageid = null)
    {
        if ($messageid === null) {
            return null;
        }

        echo $this->translate($messageid);
    }
}
