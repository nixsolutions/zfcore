<?php
/**
 * SessionController for debug module
 *
 * @category   Application
 * @package    Debug
 * @subpackage Controller
 *
 * @author Anna Pavlova <pavlova.anna@nixsolutions.com>
 *
 * @version  $Id$
 */


class Debug_SessionController extends Core_Controller_Action
{
    /**
     * Init controller plugins
     *
     */
    public function init()
    {
        /* Initialize action controller here */
        parent::init();
        /* is Dashboard Controller */
        $this->_isDashboard();

        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->_viewRenderer   = $this->_helper->getHelper('viewRenderer');

    }

     /**
     * indexAction
     *
     */
    public function indexAction()
    {

    }


    /**
     * _getCreateForm
     *
     * return create form for scaffolding
     *
     * @return  Zend_Dojo_Form
     */
    protected function _getCreateForm()
    {
        return new Debug_Model_Session_Form_Create();
    }

    /**
     * _getEditForm
     *
     * return edit form for scaffolding
     *
     * @return  Zend_Dojo_Form
     */
    protected function _getEditForm()
    {
        return new Debug_Model_Session_Form_Edit();
    }

    /**
     * _setDefaultBasePath()
     *
     * @return  void
     */
    protected function _setDefaultBasePath()
    {
        $this->_viewRenderer->setViewBasePathSpec(':moduleDir/views');
        return $this;
    }

    /**
     * _setDefaultScriptPath
     *
     * @return  void
     */
    protected function _setDefaultScriptPath()
    {
        $this->_viewRenderer->setViewScriptPathSpec(
            ':controller/:action.:suffix'
        );
        return $this;
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
        $manager = new Debug_Model_Session_Manager();
        $createForm = $this->_getCreateForm()
                           ->setAction($this->view->url());

        if ($this->_request->isPost() &&
            $createForm->isValid($this->_getAllParams())) {
            $form = $createForm->getValues();
            if ($manager->createSession($form)) {
                $this->_flashMessenger->addMessage('Successfully!');
            } else {
                $this->_flashMessenger->addMessage('Failed!');
            }
            
            $this->_helper->getHelper('redirector')->direct('index');
        } else {
            $this->view->createForm = $createForm;
            $this->_viewRenderer
                 ->setViewBasePathSpec('dashboard/scripts')
                 ->setViewScriptPathSpec('scaffold/:action.:suffix'); 
        }
        return $this;
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
        $manager = new Debug_Model_Session_Manager();
        $editForm = $this->_getEditForm()
                         ->setAction($this->view->url());

        if ($this->_request->isPost() &&
            $editForm->isValid($this->_getAllParams())) {
            // valid
            if ($manager->editSession(
                $this->_getParam('id'), $editForm->getValues()
            )) {
                $this->_flashMessenger->addMessage('Successfully!');
            } else {
                $this->_flashMessenger->addMessage('Failed!');
            }
            $this->_helper->getHelper('redirector')->direct('index');
        } else {
            // check if there is data in form
            if (!in_array(true, $editForm->getValues())) {
                $editForm->setDefaults(
                    $manager->
                    createSessionFormArray($this->_getParam('id', null))
                );
            }
             $this->view->editForm = $editForm;
        }

        $dashboard = Zend_Controller_Front::getInstance()->
                getModuleDirectory('dashboard');

        $this->_viewRenderer
             ->setViewBasePathSpec($dashboard.'/views')
             ->setViewScriptPathSpec('scaffold/:action.:suffix'); //must be here
        return $this;
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
        $manager = new Debug_Model_Session_Manager();
        $this->_helper->json($manager->deleteSession($this->_getParam('id')));
        return $this;
    }

    /**
     * storeAction
     *
     * get list of session variables
     *
     * @return  json
     */
    public function storeAction()
    {
        $start  = $this->_getParam('start', 0);
        $count  = $this->_getParam('count', 15);
        $sort   = $this->_getParam('sort', null);
        $field  = $this->_getParam('field', null);
        $filter = $this->_getParam('filter', null);

        $manager = new Debug_Model_Session_Manager();

        $sessions = $manager->createSessionArray(
            $start, $count, $sort, $field, $filter
        );
        if (empty($sessions['arr'])) {
            return $this->_forward('notfound', 'error', 'admin');
        }

        $total = $sessions['total'];
        if ($total>0) {
            $data = new Zend_Dojo_Data("id", $sessions['arr']);
            $data->setMetadata('numRows', $total);

            $this->_helper->json($data);
        } else {
            $this->_helper->json(false);
        }
        return $this;
    }
}

