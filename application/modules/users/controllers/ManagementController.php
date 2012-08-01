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
class Users_ManagementController extends Core_Controller_Action_Crud
{
    /**
     * module statistic
     *
     * @return void
     */
    public function statsAction()
    {
        $adapter = Zend_Db_Table::getDefaultAdapter();
        $this->view->totalUsers = $adapter->fetchOne('SELECT COUNT(*) FROM `users`');
        $this->view->activeUsers = $adapter->fetchOne(
            'SELECT COUNT(*) FROM `users` WHERE `status` = ?',
            array(Users_Model_User::STATUS_ACTIVE)
        );
    }

    /**
     * Add user
     */
    public function editAction()
    {
        parent::editAction();
        $this->render('create');
    }

    /**
     * Validate form param by ajax
     *
     */
    public function validateAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $table = new Users_Model_User_Table();

        $row = null;
        if ($id = $this->_getParam('id')) {
            $row = $table->getById($id);
        }
        if (!$row) {
            $row = $table->createRow();
            $form = new Users_Form_Users_Create();
        } else {
            $form = new Users_Form_Users_Edit();
            $form->populate($row->toArray());
        }
        $form->populate($this->_getAllParams());

        if ($field = $this->_getParam('validateField')) {
            $element = $form->getElement($field);
            $response = array(
                'success' => $element->isValid($this->_getParam($field)),
                'message' => $this->view->formErrors($element->getMessages()),
            );
        } else {
            $response = array(
                'success' => $form->isValid($this->_getAllParams()),
                'message' => $this->view->formErrors($form->getMessages()),
            );
        }
        if (APPLICATION_ENV != 'production') {
            $response['params'] = $this->_getAllParams();
        }
        echo $this->_helper->json($response);
    }

    /**
     * get table
     *
     * @return Pages_Model_Page_Table
     */
    protected function _getTable()
    {
        return new Users_Model_User_Table();
    }

    /**
     * get create form
     *
     * @return Pages_Form_Create
     */
    protected function _getCreateForm()
    {
        return new Users_Form_Users_Create();
    }

    /**
     * get edit form
     *
     * @return Pages_Form_Edit
     */
    protected function _getEditForm()
    {
        return new Users_Form_Users_Edit();
    }

    /**
     * custom grid filters
     *
     * @return void
     */
    protected function _prepareHeader()
    {
        $this->_addCreateButton();
        $this->_addFilter('login', 'Login');
        $this->_addFilter('email', 'E-mail');
        $this->_addFilter('firstname', 'Firstname');
        $this->_addFilter('lastname', 'Lastname');
    }

    /**
     *
     */
    protected function _prepareGrid()
    {

        $this->_grid->setColumn(
            'login',
            array(
                'name'  => 'Username',
                'type'  => Core_Grid::TYPE_DATA,
                'index' => 'login',
                'attribs' => array('width'=>'120px')
            )
        );
        $this->_grid->setColumn(
            'lastname',
            array(
                'name'  => 'Name',
                'type'  => Core_Grid::TYPE_DATA,
                'index' => 'lastname',
                'formatter' => array($this, 'nameFormatter')
            )
        );
        $this->_grid->setColumn(
            'email',
            array(
                'name'  => 'Email',
                'type'  => Core_Grid::TYPE_DATA,
                'index' => 'email',
                'attribs' => array('width'=>'180px'),
                'formatter' => array($this, 'emailFormatter')
            )
        );

        $this->_grid->setColumn(
            'role',
            array(
                'name'  => 'Role',
                'type'  => Core_Grid::TYPE_DATA,
                'index' => 'role',
                'attribs' => array('width'=>'80px')
            )
        );
        $this->_grid->setColumn(
            'status',
            array(
                'name'  => 'Status',
                'type'  => Core_Grid::TYPE_DATA,
                'index' => 'status',
                'attribs' => array('width'=>'80px'),
                'formatter' => array($this, 'statusFormatter')
            )
        );

        $this->_addEditColumn();
        $this->_addDeleteColumn();
    }

    /**
     * @param $value
     * @param $row
     * @return string
     */
    public function nameFormatter($value, $row)
    {
        return $row['firstname'] .' '. $row['lastname'];
    }
    
    /**
     * @param $value
     * @param $row
     * @return string
     */
    public function emailFormatter($value, $row)
    {
        return "<a href=\"mailto:$value\" title=\"Send email\">$value</a>";
    }

    /**
     * badge formatter
     *
     * @param $value
     * @param $row
     * @return string
     */
    public function statusFormatter($value, $row)
    {
        switch ($row['status']) {
            case (Users_Model_User::STATUS_REGISTER):
                $badge = '<span class="badge badge-warning">%s</span>';
                break;
            case (Users_Model_User::STATUS_BLOCKED):
                $badge = '<span class="badge badge-error">%s</span>';
                break;
            case (Users_Model_User::STATUS_REMOVED):
                $badge = '<span class="badge badge-inverse">%s</span>';
                break;
            case (Users_Model_User::STATUS_ACTIVE):
            default:
                $badge = '<span class="badge badge-info">%s</span>';
                break;
        }


        return sprintf($badge, $value);
    }
}