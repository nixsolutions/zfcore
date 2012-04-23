<?php
/**
 * Translate form
 *
 * @category Application
 * @package Model
 * @subpackage Form
 */
class Translate_Form_Translate_Create extends Core_Form
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName('translateForm');
        $this->setMethod('post');

        $this->addElement($this->_key());

        $this->addElement($this->_value());

        $this->addElement($this->_locale());

        $this->addElement($this->_module());

        $this->addElement($this->_submit());

        return $this;
    }

    protected function _key()
    {
        $element = new Zend_Form_Element_Text('key');
        $element->setLabel('Key');
        $element->setRequired(true)
                ->setAttrib('class', 'span4');

        return $element;
    }

    protected function _value()
    {
        $element = new Zend_Form_Element_Text('value');
        $element->setLabel('Value');
        $element->setRequired(true)
                ->setAttrib('class', 'span4');

        return $element;
    }

    protected function _locale()
    {
        $element = new Zend_Form_Element_Select('locale');
        $element->setLabel('Locale');
        $element->setRequired(true)
                ->setAttrib('class', 'span4');

        $element->addMultiOption('en', 'en');
        $element->addMultiOption('ru', 'ru');

        return $element;
    }

    protected function _module()
    {
        $element = new Zend_Form_Element_Select('module');
        $element->setLabel('Module');
        $element->setRequired(true)
                ->setAttrib('class', 'span4');

        $modules = array_keys(Zend_Controller_Front::getInstance()->getControllerDirectory());
        sort($modules);

        $element->addMultiOption('default', 'default');
        foreach ($modules as $module) {
            $element->addMultiOption($module, $module);
        }

        return $element;
    }
}