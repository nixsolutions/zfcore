<?php
/**
 * Confirm install form
 *
 * @category Application
 * @package Model
 * @subpackage Form
 */
class Install_Form_Install_Confirm extends Core_Form
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName('confirmForm');
        $this->setMethod('post');

        $this->addElement($this->_code());

        $this->addElement($this->_submit());

        return $this;
    }

    /**
     * Submit element
     *
     * @return Zend_Form_Element_Submit
     */
    protected function _submit()
    {
        $sudmit = parent::_submit();
        $sudmit->setLabel('Finish');
        return $sudmit;
    }

    /**
     * Code element
     *
     * @return Zend_Form_Element_Text
     */
    protected function _code()
    {
        $element = new Zend_Form_Element_Text('code');
        $element->setLabel('Confirm code')
                ->addDecorators($this->_decorators)
                ->setRequired(true)
                ->setAttrib('class', 'span4');

        return $element;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return Zend_Form
     */
    public function setToken($token)
    {
        $this->getElement('code')->addValidator(
            new Zend_Validate_Identical($token)
        );

        return $this;
    }
}