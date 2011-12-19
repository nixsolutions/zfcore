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
             '_addAllTableColumns',
             '_addCreateButton',
             '_addDeleteColumn',
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
     * Save action
     */
    public function saveAction()
    {
        $table = new Translate_Model_Translate_Table();
        foreach ($this->_getParam('rowset') as $rowData) {
            if ($row = $table->getById($rowData['id'])) {
                $row->setFromArray($rowData)->save();
            }
        }

        $this->_helper->flashMessenger('Updated');
        $this->_helper->redirector('index');
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
            if (!isset($translations[$row->locale][$row->module])) {
                $translations[$row->locale][$row->module] = array();
            }
            $translations[$row->locale][$row->module][$row->key] = $row->value;
        }
        foreach ($translations as $locale => $translation) {
            Translate_Model_Translate::setTranslation($translation, $locale);
        }

        $this->_helper->flashMessenger('Build successful');
        $this->_helper->redirector('index');
    }

    /**
     * Grid action
     */
    public function gridAction()
    {
        parent::gridAction();

        $this->view->form = new Translate_Form_Translate_Create();
    }

    /**
     * @see Core_Controller_Action::postDispatch()
     */
    public function postDispatch()
    {
        parent::postDispatch();

        if ('grid' == $this->_getParam('action') || 'index' == $this->_getParam('action')) {
            $this->_setDefaultScriptPath();
        }
    }
}

