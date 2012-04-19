<?php
/**
 * @category Application
 * @package Model
 * @subpackage Form
 */
class Blog_Form_Post_Create extends Core_Form
{
    /**
     * Form initialization
     *
     * @return Blog_Form_Post_Create
     */
    public function init()
    {
        $this->setName('postForm');
        $this->setMethod('post');


        $this->addElement(
            'text', 'title',
            array(
                'label' => 'Title',
                'required' => true,
                'filters' => array('StringTrim'),
                'attribs' => array('class'=>'span6')
            )
        );

        $this->addElement($this->_category());

        $teaser = new Core_Form_Element_Redactor('teaser', array(
                   'label' => 'Teaser',
                   'cols'  => 50,
                   'rows'  => 5,
                   'required' => true,
                   'filters' => array('StringTrim'),
                   'redactor' => array(
                       'imageUpload'  => '/blog/images/upload/', // url or false
                       'imageGetJson' => '/blog/images/list/',
                       'fileUpload'   => false,
                   )
                ));
        $this->addElement($teaser);

        $body = new Core_Form_Element_Redactor('body', array(
                   'label' => 'Text',
                   'cols'  => 50,
                   'rows'  => 25,
                   'required' => true,
                   'filters' => array('StringTrim'),
                   'redactor' => array(
                       'imageUpload'  => '/blog/images/upload/', // url or false
                       'imageGetJson' => '/blog/images/list/',
                       'fileUpload'   => false,
                   )
                ));
        $this->addElement($body);

        $this->addElement($this->_status());

        $this->addElement($this->_submit());

        return $this;
    }

    protected function _category()
    {
        $element = new Zend_Form_Element_Select('categoryId');
        $element->setLabel('Category')
                ->setRequired(true)
                ->addDecorators($this->_inputDecorators)
                ->setAttribs(array('class' => 'span3'));

        $categories = new Blog_Model_Category_Manager();
        $select = $categories->getDbTable()->select()->order('path');
        $select->order('path');
        $select->where('path LIKE (?)', Blog_Model_Category_Manager::CATEGORY_ALIAS.'/%');

        foreach ($categories->getDbTable()->fetchAll($select) as $row) {
            $element->addMultiOption($row->id, str_repeat("…", $row->level-1) . " " . $row->title);
        }
        return $element;
    }

    /**
     * Status Combobox
     *
     * @return Zend_Form_Element_Select
     */
    protected function _status()
    {
        $element = new Zend_Form_Element_Select('status');
        $element->setLabel('Status')->setRequired(true)->setAttribs(array('class' => 'span3'));;

        $element->addMultiOption(Blog_Model_Post::STATUS_DRAFT, 'Draft');
        $element->addMultiOption(Blog_Model_Post::STATUS_PUBLISHED, 'Published');
        $element->addMultiOption(Blog_Model_Post::STATUS_DELETED, 'Deleted');

        return $element;
    }
}