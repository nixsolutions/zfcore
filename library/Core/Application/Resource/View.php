<?php
/**
 * Core_Application_Resource_View
 *
 * @category Core
 * @package  Core_Application
 * @subpackage Resource
 */
class Core_Application_Resource_View extends Zend_Application_Resource_View
{
    /**
     * Retrieve view object
     *
     * @return Zend_View
     */
    public function getView()
    {
        if (null === $this->_view) {
            $options = $this->getOptions();
            $this->_view = new Core_View($options);

            if (isset($options['title'])) {
                $this->_view->headTitle($options['title']);
            }

            if (isset($options['doctype'])) {
                $this->_view->doctype()->setDoctype(strtoupper($options['doctype']));
                if (isset($options['charset']) && $this->_view->doctype()->isHtml5()) {
                    $this->_view->headMeta()->setCharset($options['charset']);
                }
            }
            if (isset($options['contentType'])) {
                $this->_view->headMeta()->appendHttpEquiv('Content-Type', $options['contentType']);
            }
            if (isset($options['assign']) && is_array($options['assign'])) {
                $this->_view->assign($options['assign']);
            }
        }
        return $this->_view;
    }
}
