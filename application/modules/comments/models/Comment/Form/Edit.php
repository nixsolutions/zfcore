<?php
/**
 * Create comment form
 *
 * @category Application
 * @package Model
 * @subpackage Form
 */
class Comments_Model_Comment_Form_Edit extends Comments_Model_Comment_Form_Create
{
    public function init()
    {
        parent::init();
        
        // change the label of `submit` button
        $this->getElement('submit')->setLabel('Save comment');
        $this->addElements(
            array(
                $this->_status()
            )
        );
    }

    /**
     * Create status element
     * 
     * @return Zend_Form_Element_Select 
     */
    protected function _status()
    {
        $element = new Zend_Form_Element_Select('status');
        $element->setLabel('Status')
                ->setOrder(30)
                ->setRequired(true);
 
        // add multi options
        $element->addMultiOption(Comments_Model_Comment::STATUS_ACTIVE, 'Active');
        $element->addMultiOption(Comments_Model_Comment::STATUS_REVIEW, 'Review');
        $element->addMultiOption(Comments_Model_Comment::STATUS_DELETED, 'Deleted');
        
        return $element;
    }
}