<?php
/**
 * Translate_ManagementController for admin module
 *
 * @category   Application
 * @package    Translate
 * @subpackage Controller
 *
 * @version  $Id$
 */
class Translate_ManagementController extends Core_Controller_Action_Crud
{
    /**
     * init invironment
     *
     * @return void
     */
    public function init()
    {
        /* Initialize */
        parent::init();

        $this->_beforeGridFilter(array(
             '_addCheckBoxColumn',
             '_addAllTableColumns',
             '_addEditColumn',
             '_addDeleteColumn',
             '_addCreateButton',
             '_addDeleteButton',
             '_showFilter'
        ));
    }

    /**
     * _getCreateForm
     *
     * return create form for scaffolding
     *
     * @return  Zend_Form
     */
    protected function _getCreateForm()
    {
        return new Translate_Form_Translate_Create();
    }

    /**
     * _getEditForm
     *
     * return edit form for scaffolding
     *
     * @return  Zend_Form
     */
    protected function _getEditForm()
    {
        return new Translate_Form_Translate_Create();
    }

    /**
     * _getTable
     *
     * return manager for scaffolding
     *
     * @return  Core_Model_Abstract
     */
    protected function _getTable()
    {
        return new Translate_Model_Translate_Table();
    }

    /**
     * Build Action
     */
    public function buildAction()
    {
        $table = $this->_getTable();

        $translations = array();
        foreach ($table->fetchAll() as $row) {
            if (!isset($translations[$row->locale])) {
                $translations[$row->locale] = array();
            }
            $translations[$row->locale][$row->key] = $row->value;
        }
        foreach ($translations as $locale => $translation) {
            Translate_Model_Translate::setTranslation($translation, $locale);
        }

        $this->_helper->flashMessenger('Build successful');
        $this->_helper->redirector('index');
    }
}

