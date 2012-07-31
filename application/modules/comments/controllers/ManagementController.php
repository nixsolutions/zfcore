<?php
/**
 * Comments_ManagementController for Comments module
 *
 * @category   Application
 * @package    Comments
 * @subpackage Controller
 */
class Comments_ManagementController extends Core_Controller_Action_Crud
{
    /**
     * @var Comments_Model_CommentAlias
     */
    protected $_alias;
    
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
        $this->_alias = $aliasManager->getDbTable()
            ->find($this->getRequest()->getParam('alias'))
            ->current();
    }
    
    /**
     * Declare the source used to fetch the comments
     *
     * @return Core_Grid_Adapter_AdapterInterface
     */
    protected function _getSource()
    {
        $select = $this->_getTable()
            ->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(false)
            ->joinLeft('users', 'users.id=comments.userId', array('login'))
            ->where('comments.aliasId = ?', $this->_alias->id)
            ->order('comments.created DESC');
        return new Core_Grid_Adapter_Select($select);
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
        
        if ($this->_alias && !$this->_alias->isTitleDisplayed()) {
            $form->removeTitleElement();
        }
        
        $form->setReturnUrl($this->view->url());
        
        return $form;
    }

    /**
     * custom grid filters
     *
     * @return void
     */
    protected function _prepareHeader()
    {
        $this->_addBackButton();
        $this->_addCreateButton();
        $this->_addDeleteButton();
        $this->_addFilter('body', 'Text');
    }

    /**
     * Prepare grid - remove not needed columns
     * 
     * @return void
     */
    protected function _prepareGrid()
    {
        $this->_addCheckBoxColumn();
        $this->_grid->setColumn(
            'login',
            array(
                'name'  => 'Author',
                'type'  => Core_Grid::TYPE_DATA,
                'index' => 'login',
                'attribs' => array('width'=>'120px')
            )
        );
        $this->_grid->setColumn(
            'body',
            array(
                'name'  => 'Text',
                'type'  => Core_Grid::TYPE_DATA,
                'index' => 'body',
                'formatter' => array($this, 'trimFormatter'),
            )
        );
        $this->_addCreatedColumn();
        $this->_addEditColumn();
        $this->_addDeleteColumn();
        
        if ($this->_alias && !$this->_alias->isTitleDisplayed()) {
            $this->_grid->removeColumn('title');
        }
    }

    /**
     * Add "back" button
     *
     * @return void
     */
    protected function _addBackButton()
    {
        $link = '<a href="%s" class="btn span1">&larr; Back</a>';
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
                'alias' => $this->_alias->id
            ), 
            'default', 
            true
        );
        $this->view->form = $form;
        
        $this->render('edit');
    }
}