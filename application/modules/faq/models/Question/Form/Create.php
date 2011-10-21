<?php
/**
 * Create form
 *
 * @category   Application
 * @package    Faq
 * @subpackage Form
 */
class Faq_Model_Question_Form_Create extends Zend_Form
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


        $content = new Core_Form_Element_Redactor('question');
        $content->setLabel('Question:')
            ->setRequired(true)
            ->setAttrib('redactor', array(
                'toolbar' => 'full',
                'image_upload' => $this->_getUploadImageUrl()
            ))
        ;
        $this->addElement($content);

        $content = new Core_Form_Element_Redactor('answer');
        $content->setLabel('Answer:')
            ->setRequired(true)
            ->setAttrib('redactor', array(
                'toolbar' => 'full',
                'image_upload' => $this->_getUploadImageUrl()
            ))
        ;
        $this->addElement($content);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Save');
        $this->addElement($submit);
    }

    /**
     * get upload image url
     *
     * @return string
     */
    protected function _getUploadImageUrl()
    {
        $helper = new Zend_View_Helper_Url();
        return $helper->url(array(
            'module' => 'pages',
            'controller' => 'management',
            'action' => 'upload'
        ), 'default', true);
    }
}