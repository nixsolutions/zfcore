<?php
/**
 * Editing  a message, the form
 * 
 * @category Application
 * @package Model
 * @subpackage Form
 * 
 * @version  $Id: Edit.php 1561 2009-10-16 13:31:31Z dark $
 */
class Feedback_Model_Feedback_Form_Edit extends Feedback_Model_Feedback_Form_Reply
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName('feedbackForm')
             ->setMethod(Zend_Dojo_Form::METHOD_POST);
        
        $this->addElements(
            array(
                $this->_subject(),
                $this->_body(),
                $this->_sender(),
                $this->_email(),
                $this->_template(),
                $this->_button('Edit', 'submit'),
                $this->_button('Reset', 'reset'),
                $this->_hidden('id')
            )
        );
        
        return $this;
    }
}
