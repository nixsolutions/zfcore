<?php
/**
 * Settings form
 *
 * @category Application
 * @package Model
 * @subpackage Form
 */
class Install_Form_Settings_Api extends Core_Form
{
    /**
     * Form initialization
     *
     * @return Install_Form_Settings_Api
     */
    public function init()
    {
        $this->setName('apiForm');
        $this->setMethod('post');

        $this->addElement($this->_appId());

        $this->addElement($this->_secret());

        $this->addElement($this->_googleConsumerKey());

        $this->addElement($this->_googleConsumerSecret());

        $this->addElement($this->_twitterConsumerKey());

        $this->addElement($this->_twitterConsumerSecret());

        $this->addElement($this->_submit());

        return $this;
    }

    protected function _appId()
    {
        $element = new Zend_Form_Element_Text('appId');
        $element->setLabel('Facebook AppId')
                ->addDecorators($this->_decorators)
                ->setAttrib('class', 'span6');

        return $element;
    }

    protected function _secret()
    {
        $element = new Zend_Form_Element_Text('secret');
        $element->setLabel('Facebook Secret')
                ->addDecorators($this->_decorators)
                ->setAttrib('class', 'span6');
        return $element;
    }


    protected function _googleConsumerKey()
    {
        $element = new Zend_Form_Element_Text('googleKey');
        $element->setLabel('Google Consumer Key')
                ->addDecorators($this->_decorators)
                ->setAttrib('class', 'span6');

        return $element;
    }

    protected function _googleConsumerSecret()
    {
        $element = new Zend_Form_Element_Text('googleSecret');
        $element->setLabel('Google Consumer Secret')
                ->addDecorators($this->_decorators)
                ->setAttrib('class', 'span6');

        return $element;
    }

    protected function _twitterConsumerKey()
    {
        $element = new Zend_Form_Element_Text('twitterKey');
        $element->setLabel('Twitter Consumer Key')
                ->addDecorators($this->_decorators)
                ->setAttrib('class', 'span6');

        return $element;
    }

    protected function _twitterConsumerSecret()
    {
        $element = new Zend_Form_Element_Text('twitterSecret');
        $element->setLabel('Twitter Consumer Secret')
                ->addDecorators($this->_decorators)
                ->setAttrib('class', 'span6');

        return $element;
    }
}