<?php
/**
 * Create post form
 *
 * @category Application
 * @package Model
 * @subpackage Form
 */
class Forum_Form_Admin_Create extends Core_Form
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

        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Title')
              ->setRequired(true)
              ->setAttribs(array('class'=>'span4'))
              ->addValidator(
                  'regex',
                  false,
                  array(
                      '/^[\w\s\'",.\-_]+$/i',
                      'messages' => array (
                          Zend_Validate_Regex::INVALID => 'Invalid title',
                          Zend_Validate_Regex::NOT_MATCH  => 'Invalid title'
                      )
                  )
              );
        $this->addElement($title);

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
                   'fileUpload'   => '/admin/files/upload/',
                   'fileDownload' => '/admin/files/download/?file=',
                   'fileDelete'   => '/admin/files/delete/?file=',
                ))
        );
        $body->addDecorators($this->_inputDecorators);

        $this->addElement($body);

        $this->addElement($body);
        $this->addElement($this->_category());
        $this->addElement($this->_status());

        $this->addElement($this->_submit());

        $this->addElement(new Zend_Form_Element_Hidden('pid'));
    }

    /**
     * Category Combobox
     *
     * @return Zend_Form_Element_Select
     */
    protected function _category()
    {
        $categories = new Forum_Model_Category_Manager();

        $options = array();
        foreach ($categories->getAll() as $category) {
             $options[$category->id] = $category->title;
        }

        $element = new Zend_Form_Element_Select('categoryId');
        $element->setLabel('Category')
                ->setRequired(true)
                ->addMultioptions($options)
                ->setAttribs(array('class'=>'span2'));


        return $element;
    }

    /**
     * Status combobox
     *
     * @return Zend_Form_Element_Select
     */
    protected function _status()
    {
        $status = new Zend_Form_Element_Select('status');
        $status->setLabel('Status')
               ->setRequired(true)
               ->setAttribs(array('class'=>'span2'))
               ->addMultioptions(
                   array(
                       Forum_Model_Post::STATUS_ACTIVE  => Forum_Model_Post::STATUS_ACTIVE,
                       Forum_Model_Post::STATUS_CLOSED  => Forum_Model_Post::STATUS_CLOSED,
                       Forum_Model_Post::STATUS_DELETED => Forum_Model_Post::STATUS_DELETED,
                   )
               );

        return $status;
    }
}