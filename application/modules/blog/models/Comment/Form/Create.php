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
class Blog_Model_Comment_Form_Create extends Zend_Form
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
            'textarea', 'comment',
            array(
                'label'      => 'Your comment:',
                'cols'       => '50',
                'rows'       => '5',
                'required'   => true,
                'filters'    => array('StringTrim', 'HtmlEntities'),
                'validators' => array(
                    array('validator' => 'StringLength', 'options' => array(1, 250))
                )
            )
        );
        /*
        // TinyMCE if you need
        $comment = new Core_Form_Element_TinyMCE(
            'comment', array(
                'label' => 'Your comment:',
                'cols'  => '50',
                'rows'  => '5',
                'required' => true,
                'filters' => array('StringTrim'),
                'tinyMCE' => array(
                    'mode' => "textareas",
                    'theme' => "simple",
                ),
            )
        );
        $this->addElement($comment);
        */

        $this->addElement(
            'submit',
            'submit',
            array(
                'label' => 'Add comment',
            )
        );
    }
}