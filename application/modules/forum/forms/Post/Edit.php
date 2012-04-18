<?php
/**
 * @category Application
 * @package Model
 * @subpackage Form
 */
class Forum_Form_Post_Edit extends Forum_Form_Post_Create
{
    protected function _submit()
    {
        return parent::_submit()->setLabel('Save');
    }
}