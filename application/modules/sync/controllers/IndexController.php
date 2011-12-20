<?php
/**
 * IndexController for sync module
 * Is used for synchronization database of other application
 *
 * @category   Application
 * @package    Sync
 * @subpackage Controller
 *
 * @version
 */
class Sync_IndexController extends Core_Controller_Action
{
    protected $_config = null;

    /**
     * Init method of controller `Sync_IndexController`
     */
    public function init()
    {
        parent::init();

        $this->setConfig(
            new Zend_Config_Ini(
                dirname(dirname(__FILE__)) . '/configs/sync.ini',
                APPLICATION_ENV
            )
        );

        $this->_helper->getHelper('contextSwitch')
            ->addActionContext('sync', 'xml')
            ->initContext();
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_forward('sync');
    }

    /**
     * Generate XML document containing data from db
     */
    public function syncAction()
    {
        if ($this->getRequest()->isPost()) {

            $this->_helper->layout->disableLayout();
            $this->_helper->getHelper('contextSwitch')->initContext('xml');

            $params = $this->getRequest()->getPost();

            $security
                = isset($params['security']) ? $params['security'] : array();
            $updated
                = isset($params['updated']) ? $params['updated'] : 0;

            if ((!$this->getConfig()->security)
                || $this->_isAccessable($security)
            ) {
                $data = $this->_getExportData($updated);
            } else {
                $this->render('accessdenied');
                return;
            }

            $this->view->data = $data;
        } else {
            $this->_forward('notfound', 'error', 'users');
            return;
        }
    }

    /**
     * Method check access to sync data
     *
     * @param array $security
     * @return boolean
     */
    protected function _isAccessable(array $security)
    {
        if (isset($security['username'])
            && !empty($security['username'])
            && isset($security['password'])
        ) {
            return Users_Model_Users_Manager::authenticate(
                $security['username'],
                $security['password']
            );
        }

        return false;
    }

    protected function _getExportData($updated = 0)
    {
        $data = array();

        foreach ($this->getConfig()->tables as $table => $updatedField) {
            $items = Sync_Model_Manager::getData(
                $table,
                $updated,
                $updatedField ? $updatedField : 'updated'
            );
            if (is_array($items)) $data[$table] = $items;
        }

        return $data;
    }

    /**
     * Getter method for variable $_config
     *
     * @return Zend_Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Setter method for variable $_config
     *
     * @param Zend_Config $value
     * @return Sync_IndexController
     */
    public function setConfig(Zend_Config $value)
    {
        $this->_config = $value;
        return $this;
    }
}