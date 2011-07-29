<?php
/**
 * ManagementController for users module
 *
 * @category   Application
 * @package    Users
 * @subpackage Controller
 *
 * @version  $Id: ManagementController.php 124 2010-04-21 16:57:01Z AntonShevchuk $
 */
class Users_ManagementController extends Core_Controller_Action
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

        /* is Dashboard Controller */
        $this->_isDashboard();
    }

    /**
     * Show data grid
     */
    public function indexAction()
    {
        $table = new Users_Model_Users_Table();

        //$columns = $table->info(Zend_Db_Table::COLS);
        $columns = array('id', 'login', 'firstname', 'lastname', 'email', 'role', 'status');

        if ($this->_request->isXmlHttpRequest()) {
            $data = $this->_helper->dataTables($table, null, $columns);

            $url = $this->_helper->url;
            foreach ($data['aaData'] as &$rowData) {
                $params = array('id' => $rowData['id']);

                $edit = $url->url($params, 'usersedit');
                $delete = $url->url($params, 'usersdelete');

                $rowData['editUrl'] = '<a href="'.$edit.'">Edit</a>';
                $rowData['deleteUrl'] = '<a href="'.$delete.'" class="delete" title="Are You Sure?">Delete</a>';
            }
            echo $this->_helper->json($data);
        } else {
            $this->view->columns = $columns;
        }
    }

    /**
     * Create user
     */
    public function createAction()
    {
        $form = new Users_Form_Users_Create();

        if ($this->_request->isPost()
            && $form->isValid($this->_getAllParams())) {

            $users = new Users_Model_Users_Table();

            $row = $users->createRow($form->getValues());
            $row->save();

            $this->_helper->flashMessenger('User Added Successfully!');

            $this->_helper->redirector('index');
        }
        $this->view->form = $form;
    }

    /**
     * Add user
     */
    public function editAction()
    {
        if (!$id = $this->_getParam('id')) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }

        $users = new Users_Model_Users_Table();
        if (!$row = $users->getById($id)) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }

        $form = new Users_Form_Users_Edit();
        $form->setDefaults($row->toArray(true));

        if ($this->_request->isPost()
            && $form->isValid($this->_getAllParams())) {

            $row->setFromArray($form->getValues());
            $row->save();

            $this->_helper->flashMessenger('User Updated Successfully!');

            $this->_helper->redirector('index');
        }
        $this->view->form = $form;

        $this->render('create');
    }

    /**
     * Delete user
     */
    public function deleteAction()
    {
        if (!$id = $this->_getParam('id')) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }

        $users = new Users_Model_Users_Table();
        if (!$row = $users->getById($id)) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }
        $row->delete();

        $this->_helper->flashMessenger('User Deleted Successfully!');

        $this->_helper->redirector('index');
    }
}

