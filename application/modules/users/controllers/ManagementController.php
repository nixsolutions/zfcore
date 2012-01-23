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
     * init invironment
     *
     * @return void
     */
    public function init()
    {
        /* Initialize */
        parent::init();

        $this->_beforeGridFilter(
            array(
                '_addAllTableColumns',
                '_addEditColumn',
                '_prepare',
                '_addDeleteColumn',
                '_addCreateButton',
                '_showFilter'
            )
        );

        $this->_after('_setDefaultScriptPath', array('only' => array('create', 'edit')));
    }

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

        $table = new Users_Model_Users_Table();

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
        return new Users_Model_Users_Table();
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
     *
     */
    protected function _prepare()
    {
        $this->grid
             ->removeColumn('password')
             ->removeColumn('salt')
             ->removeColumn('avatar')
             ->removeColumn('created')
             ->removeColumn('updated')
             ->removeColumn('logined')
             ->removeColumn('ip')
             ->removeColumn('count')
             ->removeColumn('hashCode')
             ->removeColumn('inform')
             ->removeColumn('fbUid')
             ->removeColumn('twId')
             ->removeColumn('gId');
    }

}