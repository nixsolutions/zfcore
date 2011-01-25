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
class Debug_Model_Session_Form_Create extends Zend_Dojo_Form
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
     * Maximux of namespace length
     * @var integer
     */
    const MAX_NAMESPACE_LENGTH = 32;

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
            array($this->_namespace(),
                  $this->_key(),
                  $this->_value(),
                  $this->_submit()
                 )
        );
        return $this;
    }
    
    /**
     * Create session key element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _key()
    {
        $key = new Zend_Dojo_Form_Element_ValidationTextBox('key');
        $key     ->setLabel('Session Key')
                 ->setRequired(true)
//                 ->setLowercase(true)
                 ->setTrim(true)
                 ->setAttribs(array('style'=>'width:60%'))
                 ->setMaxLength(self::MAX_KEY_LENGTH)
                 ->setRegExp('([a-zA-Z0-9 _-])+')
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
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _value()
    {
        $value = new Zend_Dojo_Form_Element_ValidationTextBox('value');
        $value->setLabel('Session Value')
                  ->setRequired(false)
                  ->setTrim(true)
                  ->setAttribs(array('style'=>'width:100%;height:100px;'))
                  ->setMaxLength(self::MAX_VALUE_LENGTH)
                  ->addFilter('StripTags')
                  ->addFilter('StringTrim')
                  ->addValidator(
                      'StringLength', false,
                      array(0, self::MAX_VALUE_LENGTH)
                  );
       return $value;
    }
    
    /**
     * Create session namespace
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _namespace()
    {
        $namespace = new Zend_Dojo_Form_Element_ValidationTextBox('namespace');
        $namespace->setLabel('Session Namespace')
                  ->setRequired(false)
                  ->setTrim(true)
                  ->setAttribs(array('style'=>'width:60%'))
                  ->setMaxLength(self::MAX_NAMESPACE_LENGTH)
                  ->setRegExp('^([^0-9_])([a-zA-Z0-9 _-])+')
                  ->addFilter('StripTags')
                  ->addFilter('StringTrim')
                  ->addValidator(
                      'regex', false,
                      array('/^([^0-9_])([a-zA-Z0-9 _-])+/')
                  )
                  ->addValidator(
                      'StringLength', false,
                      array(0, self::MAX_NAMESPACE_LENGTH)
                  );
        return $namespace;
    }

    /**
     * Create submit element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _submit()
    {
        $submit = new Zend_Dojo_Form_Element_SubmitButton('submit');
        $submit->setLabel('Create');
        
        return $submit;
    }
}