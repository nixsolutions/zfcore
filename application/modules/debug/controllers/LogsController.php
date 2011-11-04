<?php
/**
 * LogsController for debug module
 *
 * @category   Application
 * @package    Debug
 * @subpackage Controller
 *
 * @author Anna Pavlova <pavlova.anna@nixsolutions.com>
 *
 * @version  $Id$
 */


class Debug_LogsController extends Core_Controller_Action
{
    const ADD_LABEL_BEGIN  = 'Еще Новых';
    const ADD_LABEL_END    = 'Еще Старых';
    const ADD_NUMBER       = 20;

    private $_beginLogString = '';
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

        // load dojo
        $this->_initDojo();

        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->_viewRenderer   = $this->_helper->getHelper('viewRenderer');

        $this->_helper->AjaxContext()->addActionContext('add', 'json')->initContext('json');

    }

     /**
     * indexAction
     *
     */
    public function indexAction()
    {

    }

    /**
     * _setDefaultScriptPath
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
     * viewAction
     *
     * view page instance
     *
     * @return  void
     */
    public function viewAction()
    {
        $id = $this->_getParam('id', null);
        $manager = new Debug_Model_Logs_Manager();
        $fileArray = $manager->createFileArray($id, self::ADD_NUMBER);
        if (empty($fileArray['arr'])) {
            return $this->_forward('notfound', 'error', 'admin');
        }

        $this->_beginLogString      = $fileArray['arr'][0];
        $this->view->id             = $id;
        $this->view->logs           = $fileArray['arr'];
        $this->view->name           = $fileArray['name'];
        $this->view->addLabelBegin  = self::ADD_LABEL_BEGIN;
        $this->view->addLabelEnd    = self::ADD_LABEL_END;
        $this->view->addNumber      = self::ADD_NUMBER;
        $this->view->beginLogString = trim($fileArray['arr'][0]);
        return $this;
    }

    /**
     * addAction
     *
     * view page instance
     *
     * @return  void
     */
    public function addAction()
    {
        $id             = $this->_getParam('id', null);
        $length         = $this->_getParam('length', 10);
        $direction      = $this->_getParam('direction', null);
        $beginLogString = $this->_getParam('string', null);
//        $num += self::ADD_NUMBER;
        $manager = new Debug_Model_Logs_Manager();
        $fileArray = $manager->createAddFileArray(
            $id, $beginLogString, $length, $direction
        );
        $this->_helper->json($fileArray);

        return $this;
    }

    /**
     * storeAction
     *
     * get Dojo Grit with log files
     *
     * 
     * @return  Zend_Dojo_Grid
     */
    public function storeAction()
    {
        $start  = $this->_getParam('start', 0);
        $count  = $this->_getParam('count', 15);
        $sort   = $this->_getParam('sort', null);
        $field  = $this->_getParam('field', null);
        $filter = $this->_getParam('filter', null);

        $manager = new Debug_Model_Logs_Manager();
        $logs = $manager->createLogsArray(
            $start, $count, $sort, $field, $filter
        );
        if (empty($logs['arr'])) {
            return $this->_forward('notfound', 'error', 'admin');
        }

        $total = $logs['total'];
        if ($total>0) {
            $data = new Zend_Dojo_Data("id", $logs['arr']);
            $data->setMetadata('numRows', $total);

            $this->_helper->json($data);
        } else {
            $this->_helper->json(false);
        }
        return $this;
    }
}

