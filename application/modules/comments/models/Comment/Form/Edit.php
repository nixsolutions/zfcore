<?php
/**
 * Create comment form
 *
 * @category Application
 * @package Model
 * @subpackage Form
 *
 * @version  $Id: Comment.php 2011-11-21 11:59:34Z pavel.machekhin $
 */
class Comments_Model_Comment_Form_Edit extends Comments_Model_Comment_Form_Create
{
    public function init()
    {
        parent::init();
        
        $this->getElement('submit')->setLabel('Save comment');
        
        $this->addElements(
            array(
                $this->_status()
            )
        );
    }
    
    protected function _status()
    {
        $element = new Zend_Form_Element_Select('status');
        $element->setLabel('Status')
                ->setOrder(30)
                ->setRequired(true);

        $element->addMultiOption(Comments_Model_Comment::STATUS_ACTIVE, 'Active');
        $element->addMultiOption(Comments_Model_Comment::STATUS_REVIEW, 'Review');
        $element->addMultiOption(Comments_Model_Comment::STATUS_DELETED, 'Deleted');
        
        return $element;
    }
}