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
     * @var Comments_Model_CommentAlias
     */
    protected $alias;
    
    /**
     * Initialize
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        $aliasManager = new Comments_Model_CommentAlias_Manager();
        
        // get the Alias by the requested param
        $this->alias = $aliasManager->getDbTable()
            ->find($this->getRequest()->getParam('alias'))
            ->current();
        
        // setup the grid
        $this->_beforeGridFilter(array(
             '_addCheckBoxColumn',
             '_addAllTableColumns',
             '_prepare',
             '_addEditColumn',
             '_addDeleteColumn',
             '_addBackButton',
             '_addDeleteButton',
             '_showFilter'
        ));

    }
    
    /**
     * Declare the source used to fetch the comments
     *
     * @return Core_Grid_Adapter_AdapterInterface
     */
    protected function _getSource()
    {
        return new Core_Grid_Adapter_Select(
            $this->_getTable()
                ->select()
                ->where('aliasId = ?', $this->alias->id)
        );
    }
    
    /**
     * Declare DB table
     *
     * @return Comments_Model_Comment_Table
     */
    protected function _getTable()
    {
        return new Comments_Model_Comment_Table();
    }
    
    /**
     * Declare create form
     *
     * @return Comments_Model_Comment_Form_Create
     */
    protected function _getCreateForm()
    {
        return new Comments_Model_Comment_Form_Create();
    }

    /**
     * Declare edit form
     *
     * @return Comments_Model_Comment_Form_Edit
     */
    protected function _getEditForm()
    {
        // init form according to the CommentAlias options
        
        $form = new Comments_Model_Comment_Form_Edit();
        
        if ($this->alias && !$this->alias->isTitleDisplayed()) {
            $form->removeTitleElement();
        }
        
        $form->setReturnUrl($this->view->url());
        
        return $form;
    }
    
    /**
     * Prepare grid - remove not needed columns
     * 
     * @return void
     */
    protected function _prepare()
    {
        $this->grid->removeColumn('aliasId');
        
        if ($this->alias && !$this->alias->isTitleDisplayed()) {
            $this->grid->removeColumn('title');
        }
    }
    
    /**
     * Add "back" button
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
     * Edit comment action
     *
     * @return void
     */
    public function editAction()
    {
        // load model
        $model = $this->_loadModel();

        // init form
        $form = $this->_getEditForm()
            ->setAction($this->view->url())
            ->setDefaults($model->toArray());

        // validate form by the POST request params
        if ($this->_request->isPost() &&
            $form->isValid($this->_getAllParams())
        ) {
            // update the model
            $model->setFromArray($form->getValues())
                  ->save();
            
            // redirect to the URL that was setted to the form element
            $this->_helper->flashMessenger('Successfully');
            $this->_redirect($form->getValue('returnUrl'));
        }
        
        // set the view variables
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