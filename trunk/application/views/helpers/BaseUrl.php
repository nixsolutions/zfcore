<?php
/** @see Zend_View_Helper_BaseUrl */
require_once 'Zend/View/Helper/BaseUrl.php';

/**
 * Helper for retrieving the BaseUrl
 *
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Core_View_Helper_BaseUrl extends Zend_View_Helper_BaseUrl
{
    /**
     * Get BaseUrl
     *
     * @return string
     */
    public function getBaseUrl()
    {
        if ($this->_baseUrl === null) {
            if (Zend_Registry::isRegistered('baseUrl')) {
                $baseUrl = Zend_Registry::get('baseUrl');
            } else {
                /** @see Zend_Controller_Front */
                require_once 'Zend/Controller/Front.php';
                $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
    
                // Remove scriptname, eg. index.php from baseUrl
                $baseUrl = $this->_removeScriptName($baseUrl);
            }

            $this->setBaseUrl($baseUrl);
        }

        return $this->_baseUrl;
    }

}
