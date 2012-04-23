<?php
/**
 * Database form
 *
 * @category Application
 * @package Model
 * @subpackage Form
 */
class Install_Form_Settings_Database extends Core_Form
{
    /**
     * Form initialization
     *
     * @return Install_Form_Settings_Database
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

    protected function _host()
    {
        $element = new Zend_Form_Element_Text('host');
        $element->setLabel('Host')
                ->addDecorators($this->_decorators)
                ->setRequired(true)
                ->setAttrib('class', 'span4');

        return $element;
    }

    protected function _username()
    {
        $element = new Zend_Form_Element_Text('username');
        $element->setLabel('Username')
                ->addDecorators($this->_decorators)
                ->setRequired(true)
                ->setAttrib('class', 'span4');

        return $element;
    }

    protected function _password()
    {
        $element = new Zend_Form_Element_Password('password');
        $element->setLabel('Password')
                ->addDecorators($this->_decorators)
                ->setRequired(true)
                ->setAttrib('class', 'span4');

        return $element;
    }

    protected function _charset()
    {
        $element = new Zend_Form_Element_Text('charset');
        $element->setLabel('Charset')
                ->addDecorators($this->_decorators)
                ->setRequired(true)
                ->setAttrib('class', 'span4')
                ->setValue('utf8');

        return $element;
    }

    protected function _dbname()
    {
        $element = new Zend_Form_Element_Text('dbname');
        $element->setLabel('Database name')
                ->addDecorators($this->_decorators)
                ->setRequired(true)
                ->setAttrib('class', 'span4');

        return $element;
    }

    /**
     * Get adapters
     * @return \Zend_Form_Element_Select
     */
    protected function _adapter()
    {
        $element = new Zend_Form_Element_Select('adapter');
        $element->setLabel('Adapter')
                ->addDecorators($this->_decorators)
                ->setRequired(true)
                ->setAttrib('class', 'span4');

        $model = new Install_Model_Install_Database();

        foreach ($model->getAdapters() as $adapter) {
            $element->addMultiOption($adapter, $adapter);
        }

        return $element;
    }
}