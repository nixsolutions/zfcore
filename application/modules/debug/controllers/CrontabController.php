<?php
/**
 * CrontabController for debug module
 *
 * @category   Application
 * @package    Debug
 * @subpackage Crontab
 *
 * @author Anna Pavlova <pavlova.anna@nixsolutions.com>
 *
 * @version  $Id$
 */


class Debug_CrontabController extends Core_Controller_Action_Crud
{
    /**
     * Init controller plugins
     *
     */
    public function init()
    {
        parent::init();

        $this->_beforeGridFilter(
            array(
                '_addAllTableColumns',
                //'_prepareGrid',
                '_addEditColumn',
                '_addDeleteColumn',
                '_addCreateButton',
                '_showFilter'
            )
        );
    }

    /**
     * get source
     *
     * @return Core_Grid_Adapter_AdapterInterface
     */
    protected function _getSource()
    {
        $manager = new Debug_Model_Crontab_Manager();
        return new Core_Grid_Adapter_Array($manager->createGritArray());
    }

    /**
     * get table
     *
     * @return void
     */
    protected function _getTable()
    {
    }

    /**
     * add all columns to grid
     *
     * @return void
     */
    public function _addAllTableColumns()
    {
        $this->grid->setColumn(
            'minute', array(
                'name' => 'Minute',
                'type' => Core_Grid::TYPE_DATA,
                'index' => 'minute'
            )
        )->setColumn(
            'hour',
            array(
                'name' => 'Hour',
                'type' => Core_Grid::TYPE_DATA,
                'index' => 'hour'
            )
        )->setColumn(
            'dayOfMonth',
            array(
                'name' => 'Day of month',
                'type' => Core_Grid::TYPE_DATA,
                'index' => 'dayOfMonth'
            )
        )->setColumn(
            'month',
            array(
                'name' => 'Month',
                'type' => Core_Grid::TYPE_DATA,
                'index' => 'month'
            )
        )->setColumn(
            'dayOfWeek',
            array(
                'name' => 'Day of week',
                'type' => Core_Grid::TYPE_DATA,
                'index' => 'dayOfWeek'
            )
        )->setColumn(
            'command',
            array(
                'name' => 'Command',
                'type' => Core_Grid::TYPE_DATA,
                'index' => 'command'
            )
        );
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
        return new Debug_Model_Crontab_Form_Create();
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
        return new Debug_Model_Crontab_Form_Edit();
    }

    /**
     * createAction
     *
     * create page instance
     *
     * @return  void
     */
    public function createAction()
    {
        $manager = new Debug_Model_Crontab_Manager();
        $form = $this->_getCreateForm()->setAction($this->view->url());

        if ($this->_request->isPost() &&
            $form->isValid($this->_getAllParams())) {

            if ($manager->save($form->getValues())) {
                $this->_flashMessenger->addMessage('Successfully!');
            } else {
                $this->_flashMessenger->addMessage('Failed!');
            }

            $this->_helper->redirector('index');
        }
        $this->view->form = $form;
    }

    /**
     * editAction
     *
     * edit page instance
     *
     * @return  void
     */
    public function editAction()
    {
        if (!$id = $this->_getParam('id')) {
            throw new Zend_Controller_Action_Exception('Bad Request');
        }
        $manager = new Debug_Model_Crontab_Manager();
        $form = $this->_getEditForm()->setAction($this->view->url());

        if ($this->_request->isPost() &&
            $form->isValid($this->_getAllParams())) {
            // valid
            if ($manager->save($form->getValues(), $id)) {
                $this->_flashMessenger->addMessage('Successfully!');
            } else {
                $this->_flashMessenger->addMessage('Failed!');
            }
            $this->_helper->redirector('index');
        }
        // check if there is data in form
        $form->setDefaults($manager->getLineById($id));
        $this->view->form = $form;
    }


    /**
     * deleteAction
     *
     * delete variable from Session
     *
     * @return  json
     */
    public function deleteAction()
    {
        if (!$id = $this->_getParam('id')) {
            throw new Zend_Controller_Action_Exception('Bad Request');
        }
        $manager = new Debug_Model_Crontab_Manager();
        $this->_helper->json($manager->delete($id));
    }
}

