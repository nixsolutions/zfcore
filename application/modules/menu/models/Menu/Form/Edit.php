<?php
/**
 * Menu_Model_Menu_Form_Edit
 *
 * @category    Application
 * @package     Model_Menu
 * @subpackage  Form
 *
 * @author      Valeriu Baleyko <baleyko.v.v@gmail.com>
 * @author      Alexander Khaylo <alex.khaylo@gmail.com>
 * @copyright   Copyright (c) 2011 NIX Solutions (http://www.nixsolutions.com)
 */
class Menu_Model_Menu_Form_Edit extends Menu_Model_Menu_Form_Create
{
    public function init()
    {
        parent::init();
        return $this;
    }

    public function _submit()
    {
        return parent::_submit()->setLabel('Save')->setValue('Save');
    }
}
