<?php
/**
 * Categories_Form_Category_Edit
 *
 * @category Application
 * @package Model
 * @subpackage Form
 */
class Categories_Form_Category_Edit extends Categories_Form_Category_Create
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName('categoryForm')->setMethod('post');

        $this->addElements(
            array($this->_title(),
                 $this->_description(),
                 $this->_submit()
            )
        );
        return $this;
    }
}