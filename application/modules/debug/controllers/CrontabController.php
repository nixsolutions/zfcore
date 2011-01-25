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


class Debug_CrontabController extends Core_Controller_Action
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
        return new Debug_Model_Crontab_Form_Create();
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
        return new Debug_Model_Crontab_Form_Edit();
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
        $manager = new Debug_Model_Crontab_Manager();
        $createForm = $this->_getCreateForm()
                           ->setAction($this->view->url());

        if ($this->_request->isPost() &&
            $createForm->isValid($this->_getAllParams())) {
            $form = $createForm->getValues();
            if ($manager->createCrontabLine($form)) {
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
        $manager = new Debug_Model_Crontab_Manager();
        $editForm = $this->_getEditForm()
                         ->setAction($this->view->url());

        if ($this->_request->isPost() &&
            $editForm->isValid($this->_getAllParams())) {
            // valid
            if ($manager->editCrontabLine(
                $this->_getParam('id'),
                $editForm->getValues()
            )) {
                $this->_flashMessenger->addMessage('Successfully!');
            } else {
                $this->_flashMessenger->addMessage('Failed!');
            }
            $this->_helper->getHelper('redirector')->direct('index');
        } else {
            // check if there is data in form
            if ($this->_getParam('id', null)) {
                $editForm->setValues(
                    $manager->createCrontabFormArray(
                        $this->_getParam('id', null)
                    )
                );
            }                 
             $this->view->editForm = $editForm;
        }

        $dashboard = Zend_Controller_Front::getInstance()->
                                                getModuleDirectory('dashboard');

        $this->_viewRenderer
             ->setViewBasePathSpec($dashboard.'/views')
             ->setViewScriptPathSpec('scaffold/:action.:suffix'); //must be here
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
        $manager = new Debug_Model_Crontab_Manager();
        $manager->deleteCrontabLine($this->_getParam('id'));
        $crontabs = $manager->createGritArray(
            $start, $count, $sort, $field, $filter
        );
        $total = $crontabs['total'];
        if ($total>0) {
            $data = new Zend_Dojo_Data("id", $crontabs['arr']);
            $data->setMetadata('numRows', $total);

            $this->_helper->json($data);
        } else {
            $this->_helper->json(false);
        }
    }

    /**
     * getDojoGrid
     *
     * get list of session variables
     *
     * @return  json
     */
    public function storeAction()
    {
        $start  = $this->_getParam('start');
        $count  = $this->_getParam('count');
        $sort   = $this->_getParam('sort');
        $field  = $this->_getParam('field');
        $filter = $this->_getParam('filter');

        $manager = new Debug_Model_Crontab_Manager();
        $crontabs = $manager->createGritArray(
            $start, $count, $sort, $field, $filter
        );
        if (empty($crontabs['arr'])) {
            return $this->_forward('notfound', 'error', 'admin');
        }
        $total = $crontabs['total'];
        if ($total>0) {
            $data = new Zend_Dojo_Data("id", $crontabs['arr']);
            $data->setMetadata('numRows', $total);

            $this->_helper->json($data);
        } else {
            $this->_helper->json(false);
        }
    }
}

