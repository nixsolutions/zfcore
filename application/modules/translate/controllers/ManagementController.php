<?php
/**
 * Translate_ManagementController for admin module
 *
 * @category   Application
 * @package    Translate
 * @subpackage Controller
 */
class Translate_ManagementController extends Core_Controller_Action_Crud
{
    /**
     * prepareGrid
     *
     * @return void
     */
    protected function _prepareHeader()
    {
        $this->_addCreateButton();
    }

    /**
     * prepareGrid
     *
     * @return void
     */
    protected function _prepareGrid()
    {
        $this->_addAllTableColumns();
        $this->_addDeleteColumn();
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
            if (!isset($translations[$row->locale][$row->module])) {
                $translations[$row->locale][$row->module] = array();
            }
            $translations[$row->locale][$row->module][$row->key] = $row->value;
        }
        foreach ($translations as $locale => $translation) {
            Translate_Model_Translate::setTranslation($translation, $locale);
        }

        $this->_helper->flashMessenger('Translation build successful');
        $this->_helper->redirector('index');
    }

    /**
     * Grid action
     */
    public function gridAction()
    {
        parent::gridAction();
//        $this->_changeViewScriptPathSpec();

        $this->view->form = new Translate_Form_Translate_Create();
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

        $this->_helper->flashMessenger('All translation was updated');
        $this->_helper->redirector('index');
    }

    /**
     * @see Core_Controller_Action::postDispatch()
     */
    public function postDispatch()
    {
        parent::postDispatch();

        // custom grid and index
        if ('grid' == $this->_getParam('action') || 'index' == $this->_getParam('action')) {
            $this->_setDefaultScriptPath();
        }
    }
}

