<?php
/**
 * Create form
 *
 * @category   Application
 * @package    Faq
 * @subpackage Form
 */
class Faq_Model_Question_Form_Create extends Core_Form
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName('questionForm');
        $this->setMethod('post');

        $question = new Zend_Form_Element_Text('question');
        $question->setLabel('Question:')
            ->setRequired(true)
            ->addDecorators($this->_inputDecorators);
        $question->setAttrib('class', 'span8');

        $this->addElement($question);

        $answer = new Core_Form_Element_Redactor(
            'answer', array(
                  'label' => 'Answer:',
                  'cols'  => 50,
                  'rows'  => 15,
                  'required' => true,
                  'filters' => array('StringTrim'),
                  'redactor' => array(
                      'imageUpload'  => '/faq/images/upload/', // url or false
                      'imageGetJson' => '/faq/images/list/',
                      'fileUpload'   => false,
                  ))
        );
        $answer->addDecorators($this->_inputDecorators);
        $this->addElement($answer);

        $this->addElement($this->_submit());
    }
}