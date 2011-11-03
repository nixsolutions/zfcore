<?php
/**
 * Register session form
 *
 * @category Application
 * @package Debug
 * @subpackage Form
 *
 * @author Anna Pavlova <pavlova.anna@nixsolutions.com>
 *
 * @version  $Id$
 */
class Debug_Model_Session_Form_Create extends Zend_Form
{
    /**
     * Maximum of key length
     * @var integer
     */
    const MAX_KEY_LENGTH = 32;

    /**
     * Maximux of value length
     * @var integer
     */
    const MAX_VALUE_LENGTH = 3000;

    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName('sessionCreateForm')
             ->setMethod('post');

        $this->addElements(
            array($this->_key(),
                  $this->_value(),
                  $this->_submit()
                 )
        );
        return $this;
    }

    /**
     * Create session key element
     *
     * @return object Zend_Form_Element_Text
     */
    protected function _key()
    {
        $key = new Zend_Form_Element_Text('key');
        $key     ->setLabel('Session Key')
                 ->setRequired(true)
                 ->setAttribs(array('style'=>'width:60%'))
                 ->addFilter('StripTags')
                 ->addFilter('StringTrim')
                 ->addValidator('regex', false, array('/([a-zA-Z0-9 _-])+/'))
                 ->addValidator(
                     'StringLength', false,
                     array(0, self::MAX_KEY_LENGTH)
                 );

        return $key;
    }

    /**
     * Create session value element
     *
     * @return object Zend_Form_Element_Textarea
     */
    protected function _value()
    {
        $value = new Zend_Form_Element_Textarea('value');
        $value->setLabel('Session Value')
                  ->setRequired(false)
                  ->setAttribs(array('style'=>'width:100%;height:100px;'))
                  ->addFilter('StripTags')
                  ->addFilter('StringTrim')
                  ->addValidator(
                      'StringLength', false,
                      array(0, self::MAX_VALUE_LENGTH)
                  );
       return $value;
    }

    /**
     * Create submit element
     *
     * @return object Zend_Form_Element_Submit
     */
    protected function _submit()
    {
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Create');

        return $submit;
    }
}