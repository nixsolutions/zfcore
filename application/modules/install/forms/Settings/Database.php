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
class Install_Form_Settings_Database extends Zend_Form
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName('databaseForm');
        $this->setMethod('post');

        $this->addElement($this->_adapter());

        $this->addElement($this->_host());

        $this->addElement($this->_username());

        $this->addElement($this->_password());

        $this->addElement($this->_dbname());

        $this->addElement($this->_charset());

        $this->addElement($this->_submit());

        return $this;
    }

    protected function _submit()
    {
        $sudmit = new Zend_Form_Element_Submit('submit');
        $sudmit->setLabel('Save & Next >');
        return $sudmit;
    }

    protected function _host()
    {
        $element = new Zend_Form_Element_Text('host');
        $element->setLabel('Host');
        $element->setRequired(true)->setAttrib('style', 'width:100%');

        return $element;
    }

    protected function _username()
    {
        $element = new Zend_Form_Element_Text('username');
        $element->setLabel('Username');
        $element->setRequired(true)->setAttrib('style', 'width:100%');

        return $element;
    }

    protected function _password()
    {
        $element = new Zend_Form_Element_Password('password');
        $element->setLabel('Password');
        $element->setRequired(true)->setAttrib('style', 'width:100%');

        return $element;
    }

    protected function _charset()
    {
        $element = new Zend_Form_Element_Text('charset');
        $element->setLabel('Charset');
        $element->setAttrib('style', 'width:100%');

        $element->setValue('utf8');

        return $element;
    }

    protected function _dbname()
    {
        $element = new Zend_Form_Element_Text('dbname');
        $element->setLabel('Database name');
        $element->setAttrib('style', 'width:100%');

        return $element;
    }

    /**
     * Get adapters
     */
    protected function _adapter()
    {
        $element = new Zend_Form_Element_Select('adapter');
        $element->setLabel('Adapter');
        $element->setRequired(true)->setAttrib('style', 'width:100%');

        $model = new Install_Model_Install_Database();

        foreach ($model->getAdapters() as $adapter) {
            $element->addMultiOption($adapter, $adapter);
        }

        return $element;
    }
}