<?php
/**
 * @category Application
 * @package Model
 * @subpackage Form
 */
class Forum_Form_Post_Create extends Core_Form
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName('postForm');
        $this->setMethod('post');

        $this->addElement(
            'text', 'title',
            array(
                'label' => 'Topic title',
                'required' => true,
                'filters' => array('StringTrim'),
                'attribs' => array('class'=>'span6')
            )
        );

        $body = new Core_Form_Element_Redactor(
            'body', array(
                'label' => 'Text',
                'cols'  => 50,
                'rows'  => 25,
                'required' => true,
                'filters' => array('StringTrim'),
                'redactor' => array(
                   'imageUpload'  => '/forum/images/upload/', // url or false
                   'imageGetJson' => '/forum/images/list/',
                   'fileUpload'   => false
                ))
        );

        $this->addElement($body);
        $this->addElement($this->_submit());

        return $this;
    }

}