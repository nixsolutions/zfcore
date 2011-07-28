<?php
/**
 * Edit page form
 *
 * @category Application
 * @package Model
 * @subpackage Form
 *
 * @version  $Id: Create.php 163 2010-07-12 16:30:02Z AntonShevchuk $
 */
class Forum_Model_Comment_Form_Create extends Zend_Form
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setMethod('post');

        $this->addElement(
            'text', 'title',
            array(
                'label'      => 'Comment title:',
                'cols'       => '50',
                'rows'       => '5',
                'required'   => true,
                'filters'    => array('StringTrim', 'HtmlEntities'),
                'validators' => array(
                    array('validator' => 'StringLength', 'options' => array(1, 250))
                )
            )
        );

        $this->addElement(
            'textarea', 'body',
            array(
                'label'      => 'Your comment:',
                'cols'       => '50',
                'rows'       => '5',
                'required'   => true,
                'filters'    => array('StringTrim', 'HtmlEntities'),
                'validators' => array(
                    array('validator' => 'StringLength', 'options' => array(1, 1000))
                )
            )
        );




        $this->addElement(
            'submit',
            'submit',
            array(
                'label' => 'Add comment',
            )
        );
    }
}