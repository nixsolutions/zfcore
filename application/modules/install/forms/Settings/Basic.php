<?php
/**
 * Database form
 *
 * @category Application
 * @package Model
 * @subpackage Form
 *
 * @version  $Id$
 */
class Install_Form_Settings_Basic extends Zend_Form
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName('settingsForm');
        $this->setMethod('post');

        $this->addElement($this->_baseUrl());

        $this->addElement($this->_timezone());

        $this->addElement($this->_title());

        $this->addElement($this->_uploadDir());

        $this->addElement($this->_submit());

        return $this;
    }

    protected function _submit()
    {
        $sudmit = new Zend_Form_Element_Submit('submit');
        $sudmit->setLabel('Save');
        return $sudmit;
    }

    protected function _baseUrl()
    {
        $element = new Zend_Form_Element_Text('baseUrl');
        $element->setLabel('Base Url');
        $element->setRequired(true)->setAttrib('style', 'width:100%');
        $element->setValue('/');

        return $element;
    }

    protected function _timezone()
    {
        $element = new Zend_Form_Element_Select('timezone');
        $element->setLabel('Timezone');
        $element->setRequired(true)->setAttrib('style', 'width:100%');

        foreach (timezone_identifiers_list() as $id) {
            $element->addMultiOption($id, $id);
        }

        return $element;
    }

    protected function _title()
    {
        $element = new Zend_Form_Element_Text('title');
        $element->setLabel('Sitename');
        $element->setRequired(true)->setAttrib('style', 'width:100%');

        $element->setValue('My ZFCore Site');

        return $element;
    }

    protected function _uploadDir()
    {
        $path = APPLICATION_PATH . '/../public';

        $element = new Zend_Form_Element_Text('uploadDir');
        $element->setLabel('Upload Directory');
        $element->setRequired(true)->setAttrib('style', 'width:100%');

        $callback = new Zend_Validate_Callback(array($this, 'isWritable'));
        $callback->setMessage(
            "Directory '" . realpath($path) . "/%value%' must be writeable",
            Zend_Validate_Callback::INVALID_VALUE
        );
        $element->addValidator($callback);
        $element->setValue('uploads');

        return $element;
    }

    public function isWritable($path)
    {
        $path = APPLICATION_PATH . '/../public/' . $path;
        return is_dir($path) && is_writable($path);
    }
}