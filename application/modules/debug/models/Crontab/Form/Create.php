<?php
/**
 * Register session form
 *
 * @category   Application
 * @package    Crontab
 * @subpackage Form
 *
 * @author     Anna Pavlova <pavlova.anna@nixsolutions.com>
 */
class Debug_Model_Crontab_Form_Create extends Core_Form
{
    /**
     * Maximum of Crontab line length
     *
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
     * @return Debug_Model_Crontab_Form_Create
     */
    public function init()
    {
        $this->setName( 'crontabCreateForm' )
            ->setMethod( 'post' );

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
     * get table
     *
     * @return void
     */
    protected function _getTable()
    {
    }

    /**
     * Create Crontab minute element
     *
     * @return object Zend_Form_Element_Text
     */
    protected function _minute()
    {
        $value = new Zend_Form_Element_Text('minute');
        $value->setLabel( 'Minute' )
            ->addDecorators( $this->_inputDecorators )
            ->setRequired( true )
            ->setAttribs( array('class'=> 'span1') )
            ->addValidator(
            new Zend_Validate_InArray(range( 0, 59 ))
        );

        return $value;
    }

    /**
     * Create Crontab hour element
     *
     * @return object Zend_Form_Element_Text
     */
    protected function _hour()
    {
        $value = new Zend_Form_Element_Text('hour');
        $value->setLabel( 'Hour' )
            ->addDecorators( $this->_inputDecorators )
            ->setRequired( true )
            ->setAttribs( array('class'=> 'span1') )
            ->addValidator(
            new Zend_Validate_InArray(range( 0, 23 ))
        );
        return $value;
    }

    /**
     * Create Crontab dayOfMonth element
     *
     * @return object Zend_Form_Element_Text
     */
    protected function _dayOfMonth()
    {
        $value = new Zend_Form_Element_Text('dayOfMonth');
        $value->setLabel( 'Day Of Month' )
            ->addDecorators( $this->_inputDecorators )
            ->setRequired( true )
            ->setAttribs( array('class'=> 'span2') )
            ->addValidator(
            new Zend_Validate_InArray(range( 1, 31 ))
        );
        return $value;
    }

    /**
     * Create Crontab month element
     *
     * @return object Zend_Form_Element_Select
     */
    protected function _month()
    {
        $value = new Zend_Form_Element_Select('month');
        $value->setLabel( 'Month' )
            ->addDecorators( $this->_inputDecorators )
            ->setRequired( true )
            ->setAttribs( array('class'=> 'span2') )
            ->setMultiOptions( $this->_selectOptionsMonth );
        return $value;
    }

    /**
     * Create Crontab dayOfWeek element
     *
     * @return object Zend_Form_Element_Text
     */
    protected function _dayOfWeek()
    {
        $value = new Zend_Form_Element_Select('dayOfWeek');
        $value->setLabel( 'Day Of Week' )
            ->addDecorators( $this->_inputDecorators )
            ->setRequired( true )
            ->setAttribs( array('class'=> 'span2') )
            ->setMultiOptions( $this->_selectOptionsDayOfWeek );
        return $value;
    }

    /**
     * Create Crontab command element
     *
     * @return object Zend_Form_Element_Text
     */
    protected function _command()
    {
        $value = new Zend_Form_Element_Text('command');
        $value->setLabel( 'Command' )
            ->addDecorators( $this->_inputDecorators )
            ->setRequired( true )
            ->setAttribs( array('class'=> 'span8') )
            ->addFilter( 'StripTags' )
            ->addFilter( 'StringTrim' )
            ->addValidator(
            'StringLength', false,
            array(0,
                self::MAX_LINE_LENGTH)
        );
        return $value;
    }
}