<?php
/**
 * @category Application
 * @package Model
 * @subpackage Form
 */
class Forum_Form_Post_Edit extends Forum_Form_Post_Create
{
    /**
     * init
     *
     * @return Forum_Form_Post_Edit
     */
    public function init()
    {
        parent::init();
        $this->addElement($this->_status());
        return $this;
    }


    protected function _submit()
    {
        return parent::_submit()->setLabel('Save');
    }


    protected function _status()
    {
        $status = new Zend_Form_Element_Select('status');
        $status->setLabel('Status');
        $status->setRequired(true);
        $status->addMultiOptions(
            array(
                Forum_Model_Post::STATUS_ACTIVE  => Forum_Model_Post::STATUS_ACTIVE,
                Forum_Model_Post::STATUS_CLOSED  => Forum_Model_Post::STATUS_CLOSED,
                Forum_Model_Post::STATUS_DELETED => Forum_Model_Post::STATUS_DELETED)
        );

        return $status;
    }

    protected function _category()
    {
        $category = new Zend_Form_Element_Select('categoryId');
        $category->setLabel('categoryId');
        $category->setRequired(true);

        $cats = new Forum_Model_Category_Manager();
        foreach ($cats->getAll() as $cat) {
            $category->addMultiOption($cat->id, $cat->title);
        }

        return $category;
    }
}