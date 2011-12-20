<?php
/**
 * Comments_ManagementController for Comments module
 *
 * @category   Application
 * @package    Comments
 * @subpackage Controller
 *
 * @version  $Id: ManagementController.php 2011-11-21 11:59:34Z pavel.machekhin $
 */
class Comments_ManagementController extends Core_Controller_Action_Crud
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

        $aliasManager = new Comments_Model_CommentAlias_Manager();
        
        $this->alias = $aliasManager->getDbTable()
            ->find($this->getRequest()->getParam('alias'))
            ->current();
        
        $this->_beforeGridFilter(array(
             '_addCheckBoxColumn',
             '_addAllTableColumns',
             '_prepare',
             '_addEditColumn',
             '_addDeleteColumn',
             '_addBackButton',
//             '_addCreateButton',
             '_addDeleteButton',
             '_showFilter'
        ));

    }
    
    /**
     * get source
     *
     * @return Core_Grid_Adapter_AdapterInterface
     */
    protected function _getSource()
    {
        return new Core_Grid_Adapter_Select($this->_getTable()->select()->where('aliasId = ?', $this->alias->id));
    }
    
    /**
     * Get table
     *
     * @return Pages_Model_Page_Table
     */
    protected function _getTable()
    {
        return new Comments_Model_Comment_Table();
    }
    
    /**
     * get create form
     *
     * @return Comments_Model_Comment_Form_Create
     */
    protected function _getCreateForm()
    {
        return new Comments_Model_Comment_Form_Create();
    }

    /**
     * get edit form
     *
     * @return Comments_Model_Comment_Form_Create
     */
    protected function _getEditForm()
    {
        $form = new Comments_Model_Comment_Form_Edit();
        
        if ($this->alias && !$this->alias->isTitleDisplayed()) {
            $form->removeTitleElement();
        }
        
        $form->setReturnUrl($this->view->url());
        
        return $form;
    }
    
    protected function _prepare()
    {
        $this->grid->removeColumn('aliasId');
        
        if ($this->alias && !$this->alias->isTitleDisplayed()) {
            $this->grid->removeColumn('title');
        }
    }
    
    /**
     * add create button
     *
     * @return void
     */
    protected function _addBackButton()
    {
        $link = '<a href="%s" class="button">&larr; Back</a>';
        $url = $this->getHelper('url')->url(
            array(
                'module' => 'comments', 
                'controller' => 'aliases', 
                'action' => 'index'
            ), 
            'default', 
            true
        );
        $this->view->placeholder('grid_buttons')->back = sprintf($link, $url);
    }
    
    /**
     * edit
     *
     * @return void
     */
    public function editAction()
    {
        $model = $this->_loadModel();

        $form = $this->_getEditForm()
            ->setAction($this->view->url())
            ->setDefaults($model->toArray());

        if ($this->_request->isPost() &&
            $form->isValid($this->_getAllParams())
        ) {
            $model->setFromArray($form->getValues())
                  ->save();

            $this->_helper->flashMessenger('Successfully');
            $this->_redirect($form->getValue('returnUrl'));
        }
        
        $this->view->backUrl = $this->view->url(
            array(
                'module' => 'comments', 
                'controller' => 'management', 
                'action' => 'index', 
                'alias' => $this->alias->id
            ), 
            'default', 
            true
        );
        $this->view->form = $form;
        
        $this->render('edit');
    }
}