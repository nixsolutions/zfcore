<?php
/**
 * Login form
 *
 * @category Application
 * @package Model
 * @subpackage Form
 *
 * @version  $Id: Edit.php 146 2010-07-05 14:22:20Z AntonShevchuk $
 */
class Blog_Form_Post_Edit extends Blog_Form_Post_Create
{
    protected function _submit()
    {
        return parent::_submit()->setLabel('Save');
    }
}