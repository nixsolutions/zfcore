<?php
/**
 * Register user form
 * 
 * @category Application
 * @package Model
 * @subpackage Form
 * 
 * @version  $Id: Send.php 210 2010-11-23 12:17:03Z andreyalek $
 */
class Model_Mail_Form_Send extends Model_Mail_Form_Create
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->addElementPrefixPath(
            'Model_Mail_Form_Validate',
            APPLICATION_PATH . '/models/Mail/Form/Validate',
            'validate'
        )
        ->setName('mailSendForm')
        ->setMethod('post');
        
        $this->addElements(
            array(
                $this->_subject(),
                $this->_body(),
                $this->_fromName(),
                $this->_fromEmail(),
                $this->_filter(),
                $this->_filterInput(),
                $this->_ignore(),
                $this->_signature(),
                $this->_submit()
            )
        );
        return $this;
    }
    
    /**
     * Create submit element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _submit()
    {
        return parent::_submit()->setLabel('Send');
    }
    
    /**
     * Create element filter
     * @todo remove hadcode, fix logic
     */
    protected function _filter()
    {
        $group = new Zend_Dojo_Form_Element_ComboBox('filter');
        $group->setLabel('Select filter')
              ->setRequired(true)
              ->setAttribs(array('style'=>'width:60%'))
              ->setMultiOptions(
                  array(
                      'to all'                   => 'to all',
                      'to all active'            => 'to all active',
                      'to disabled'              => 'to disabled',
                      'to not active last month' => 'to not active last month',
                      'to not activated'         => 'to not activated',
                      'custom email'             => 'custom email'
                  )
              );
         return $group;
    }
    
    /**
     * Create element input to filter
     */
    protected function _filterInput()
    {
        $filter = new Zend_Dojo_Form_Element_ValidationTextBox('filterInput');
        $filter->setLabel('Type Email')
                    ->setRequired(true)
                    ->setAttribs(array('style'=>'width:60%;'))
                    ->addValidator('CustomEmail');
        return $filter;
    }
    
    /**
     * Create element ignore
     */
    protected function _ignore()
    {
        $ignore = new Zend_Dojo_Form_Element_CheckBox('ignore');
        $ignore->setLabel('Ingore user settings');
        return $ignore;
    }

    public function isValid($data)
    {
        if ($data['filter'] != 'custom email') {
            $this->getElement('filterInput')
                 ->setRequired(false)
                 ->removeValidator('CustomEmail');
        }
        
        return parent::isValid($data);
    }

}