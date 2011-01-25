<?php
/**
 * Register session form
 * 
 * @category Application
 * @package Crontab
 * @subpackage Form
 *
 * @author Anna Pavlova <pavlova.anna@nixsolutions.com>
 *
 * @version  $Id$
 */
class Debug_Model_Crontab_Form_Create extends Zend_Dojo_Form
{
   /**
     * Maximum of Crontab line length
     * @var integer
     */
    const MAX_LINE_LENGTH = 32;


    private $_selectOptionsMonth = array(
                                      '1'  => 'January',
                                      '2'  => 'Fabuary',
                                      '3'  => 'March',
                                      '4'  => 'April',
                                      '5'  => 'May',
                                      '6'  => 'June',
                                      '7'  => 'July',
                                      '8'  => 'August',
                                      '9'  => 'September',
                                      '10' => 'October',
                                      '11' => 'November',
                                      '12' => 'December',
                                 );

    private $_selectOptionsDayOfWeek = array(
                                          '7'  => 'Sunday',
                                          '1'  => 'Monday',
                                          '2'  => 'Tuesday',
                                          '3'  => 'Wednesday',
                                          '4'  => 'Thursday',
                                          '5'  => 'Friday',
                                          '6'  => 'Saturday',
                                       );

    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName('crontabCreateForm')
             ->setMethod('post');
        
        $this->addElements(
            array($this->_minute(),
                  $this->_hour(),
                  $this->_dayOfMonth(),
                  $this->_month(),
                  $this->_dayOfWeek(),
                  $this->_command(),
                  $this->_submit()
                 )
        );
        return $this;
    }
    
    /**
     * Create Crontab minute element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _minute()
    {
        $value = new Zend_Dojo_Form_Element_ValidationTextBox('minute');
        $value   ->setLabel('Minute')
                 ->setRequired(true)
                 ->setTrim(true)
                 ->setAttribs(array('style'=>'width:40%'))
                 ->setMaxLength(self::MAX_LINE_LENGTH)
                 ->setRegExp('[0-9-,]+')
                 ->addFilter('StripTags')
                 ->addFilter('StringTrim')
                 ->addValidator('regex', false, array('/[0-9-,]+/'))
                 ->addValidator(
                     'StringLength', false,
                     array(0,
                           self::MAX_LINE_LENGTH)
                 );
                 
        return $value;
    }
    
    /**
     * Create Crontab hour element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _hour()
    {
        $value = new Zend_Dojo_Form_Element_ValidationTextBox('hour');
        $value   ->setLabel('Hour')
                 ->setRequired(true)
                 ->setTrim(true)
                 ->setAttribs(array('style'=>'width:40%'))
                 ->setMaxLength(self::MAX_LINE_LENGTH)
                 ->setRegExp('[0-9-,]+')
                 ->addFilter('StripTags')
                 ->addFilter('StringTrim')
                 ->addValidator('regex', false, array('/[0-9-,\*]+/'))
                 ->addValidator(
                     'StringLength', false,
                     array(0,
                           self::MAX_LINE_LENGTH)
                 );
        return $value;
    }
    
    /**
     * Create Crontab dayOfMonth element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _dayOfMonth()
    {
        $value = new Zend_Dojo_Form_Element_ValidationTextBox('dayOfMonth');
        $value   ->setLabel('Day Of Month')
                 ->setRequired(true)
                 ->setTrim(true)
                 ->setAttribs(array('style'=>'width:40%'))
                 ->setMaxLength(self::MAX_LINE_LENGTH)
                 ->setRegExp('[0-9-,]+')
                 ->addFilter('StripTags')
                 ->addFilter('StringTrim')
                 ->addValidator('regex', false, array('/[0-9-,\*]+/'))
                 ->addValidator(
                     'StringLength', false,
                     array(0,
                           self::MAX_LINE_LENGTH)
                 );
        return $value;
    }

    /**
     * Create Crontab month element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _month()
    {
        $value = new Zend_Dojo_Form_Element_ComboBox('month');
        $value    ->setLabel('Month')
                  ->setRequired(true)
                  ->setAttribs(array('style'=>'width:40%'))
                  ->addFilter('StripTags')
                  ->addFilter('StringTrim')
                  ->addValidator('regex', false, array('/[a-zA-Z0-9-,\*]+/'))
                  ->addValidator(
                      'StringLength', false,
                      array(0,
                           self::MAX_LINE_LENGTH)
                  )
//                  ->setValue(1)
                  ->setAutoComplete(true)
                  ->setMultiOptions($this->_selectOptionsMonth);
        return $value;
    }

    /**
     * Create Crontab dayOfWeek element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _dayOfWeek()
    {
        $value = new Zend_Dojo_Form_Element_ComboBox('dayOfWeek');
        $value    ->setLabel('Day Of Week')
                  ->setRequired(true)
                  ->setAttribs(array('style'=>'width:40%'))
                  ->addFilter('StripTags')
                  ->addFilter('StringTrim')
                  ->addValidator('regex', false, array('/[a-zA-Z0-7-,\*]+/'))
                  ->addValidator(
                      'StringLength', false,
                      array(0,
                           self::MAX_LINE_LENGTH)
                  )
//                  ->setValue(1)
                  ->setAutoComplete(true)
                  ->setMultiOptions($this->_selectOptionsDayOfWeek);
        return $value;
    }

    /**
     * Create Crontab command element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _command()
    {
        $value = new Zend_Dojo_Form_Element_ValidationTextBox('command');
        $value   ->setLabel('Command')
                 ->setRequired(true)
                 ->setTrim(true)
                 ->setAttribs(array('style'=>'width:40%'))
                 ->setMaxLength(self::MAX_LINE_LENGTH)
                 ->setRegExp('[a-zA-Z0-9-,]+')
                 ->addFilter('StripTags')
                 ->addFilter('StringTrim')
                 ->addValidator('regex', false, array('/[a-zA-Z0-9-,\*]+/'))
                 ->addValidator(
                     'StringLength', false,
                     array(0,
                           self::MAX_LINE_LENGTH)
                 );
        return $value;
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