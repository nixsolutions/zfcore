<?php
/**
 * Edit user form
 *
 * @category Application
 * @package Model
 * @subpackage Form
 *
 * @version  $Id: Edit.php 47 2010-02-12 13:17:34Z AntonShevchuk $
 */
class Users_Form_Users_Edit extends Users_Form_Users_Create
{
    /**
     * Form initialization
     *
     * @return Users_Form_Users_Edit
     */
    public function init()
    {
        parent::init()->removeElement('login');

        $this->getElement('password')->setRequired(false);

        return $this;
    }

    /**
     * Set default values for elements
     *
     * Sets values for all elements specified in the array of $defaults.
     *
     * @param  array $defaults
     * @return Zend_Form
     */
    public function setDefaults(array $defaults)
    {
        if (isset($defaults['id'])) {
            $this->getElement('email')->getValidator('Db_NoRecordExists')
            ->setExclude(
                array('field' => 'id', 'value' => $defaults['id'])
            );
        }
        if (isset($defaults['password'])) {
            unset($defaults['password']);
        }
        return parent::setDefaults($defaults);
    }
}