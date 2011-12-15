<?php

class Core_Application_Resource_Translate
    extends Zend_Application_Resource_Translate
{
    /**
     * Init Resource
     */
    public function init()
    {
        //return;
        if (!isset($this->_options['content'], $this->_options['data'])) {

            $this->getBootstrap()->bootstrap('Modules');

            $this->_options['content'] = Translate_Model_Translate::getTranslationPath();
            $this->_options['adapter'] = Translate_Model_Translate::ADAPTER;
        }
        $translate = $this->getTranslate();
        $front = $this->getBootstrap()->bootstrap('frontController')
                                      ->getResource('frontController');


        $front->registerPlugin(new Core_Controller_Plugin_Translate($translate));

        return $translate;
    }
}