<?php
/**
 * Install_IndexController for install module
 *
 * @category   Application
 * @package    Install
 * @subpackage Controller
 *
 * @version  $Id$
 */
class Install_IndexController extends Core_Controller_Action
{
    const SESSION_KEY = 'Install';

    /**
     * @var Zend_Session_Namespace
     */
    protected $_store;

    /**
     * @var array
     */
    protected $_folders = array(
        Install_Model_Install::CACHE_DIR,
        Install_Model_Install::LANGUAGES_DIR,
        Install_Model_Install::LOGS_DIR,
        Install_Model_Install::SESSION_DIR,
    );

    protected $_config = '/configs/application.yaml';

    /**
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        $this->_helper->layout->setLayout('install/layout');

        $config = new Core_Config_Yaml(
            APPLICATION_PATH . $this->_config . '.dist',
            null,
            array(
                'allowModifications' => true,
                'ignoreConstants' => true,
                'skipExtends' => true
            )
        );

        $this->_store = new Zend_Session_Namespace(self::SESSION_KEY);

        if (!$this->_store->config) {
            $this->_store->config = $config;
        }
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        if (!$this->_store->progress) {
            $this->_store->progress = array(
                'folders'    => false,
                'settings'   => false,
                'database'   => false,
                'mail'       => false,
                'confirm'    => false,
                'migrations' => false,
                'finish'     => false
            );
        }

        foreach ($this->_store->progress as $action => $status) {
            if (false == $status) {
                return $this->_forward($action);
            }
        }

    }

    /**
     * Folders action
     */
    public function foldersAction()
    {
        $unwritable = array();
        foreach ($this->_folders as $folder) {
            $folder = APPLICATION_PATH . $folder;
            if (!is_writable($folder)) {
                @chmod($folder, 0777);
            }
            if (!is_writable($folder)) {
                $unwritable[] = $folder;
            }
        }

        if (!empty($unwritable)) {
            $this->view->unwritable = $unwritable;
        } else {

            $config = $this->_store->config->production->resources;
            $config->session->save_path = APPLICATION_PATH . Install_Model_Install::SESSION_DIR;

            $this->_store->progress['folders'] = true;
            $this->_helper->redirector('index');
        }
    }

    /**
     * Settings action
     */
    public function settingsAction()
    {
        $form = new Install_Form_Install_Settings();

        if ($this->_request->isPost()
            && $form->isValid($this->_getAllParams())) {

            $config = $this->_store->config->production;

            $config->phpSettings->date->timezone = $form->getValue('timezone');
            $config->resources->frontController->baseUrl = $form->getValue('baseUrl');
            $config->resources->view->title = $form->getValue('title');
            $config->uploadDir = $form->getValue('uploadDir');

            $this->_store->progress['settings'] = true;
            $this->_helper->redirector('index');
        }

        $this->view->form = $form;
    }


    /**
     * Mail action
     */
    public function mailAction()
    {
        $form = new Install_Form_Install_Mail();

        if ($this->_request->isPost()
        && $form->isValid($this->_getAllParams())) {

            $config = $this->_store->config->production->resources->mail;

            $config->transport->type = $form->getValue('type');
            $config->transport->host = $form->getValue('host');
            $config->transport->post = $form->getValue('post');

            $config->transport->defaultFrom->email = $form->getValue('email');
            $config->transport->defaultFrom->name = $form->getValue('name');

            $this->_store->progress['mail'] = true;
            $this->_helper->redirector('index');
        }

        $this->view->form = $form;
    }

    /**
     * Database Action
     */
    public function databaseAction()
    {
        $form = new Install_Form_Install_Database();

        if ($this->_request->isPost()
            && $form->isValid($this->_getAllParams())) {

            $config = array(
                'adapter' => $form->getValue('adapter'),
                'params' => array(
                    'host'     => $form->getValue('host'),
                    'username' => $form->getValue('username'),
                    'password' => $form->getValue('password'),
                    'dbname'   => $form->getValue('dbname'),
                    'charset'  => $form->getValue('charset')
                )
            );
            $config = new Zend_Config($config);

            $db = Zend_Db::factory($config);
            try {
                $db->getConnection();

                $this->_store->config->production->resources->db = $config;

                $this->_store->progress['database'] = true;
                $this->_helper->redirector('index');

            } catch (Zend_Db_Adapter_Exception $e) {
                $this->view->messages = array($e->getMessage());
            }
        }

        $this->view->form = $form;
    }

    /**
     * Migrations Action
     */
    public function migrationsAction()
    {
        $config = $this->_store->config->production->resources->db;

        $db = Zend_Db::factory($config);
        Zend_Db_Table_Abstract::setDefaultAdapter($db);

        $options = array(
            'projectDirectoryPath' => APPLICATION_PATH . '/..',
            'modulesDirectoryPath' => APPLICATION_PATH . '/../modules',
        );

        $manager = new Core_Migration_Manager($options);

        $manager->up();

        $this->_helper->flashMessenger('Migrations rolled up');
        $this->_store->progress['migrations'] = true;

        $this->_store->progress['migrations'] = true;
        $this->_helper->redirector('index');
    }

    /**
     * Confirm Action
     */
    public function confirmAction()
    {
        $form = new Install_Form_Install_Confirm();

        if (!$this->_store->confirmCode
           || !is_file($this->_store->confirmFile)) {

            $model = new Install_Model_Install();
            $this->_store->confirmCode = $model->generateCode();

            $this->_store->confirmFile = $model->saveCode($this->_store->confirmCode);
        }
        $form->setToken($this->_store->confirmCode);

        if ($this->_request->isPost()
            && $form->isValid($this->_getAllParams())) {

            @unlink($this->_store->confirmFile);

            $this->_store->progress['confirm'] = true;

            $this->_store->progress['confirm'] = true;
            $this->_helper->redirector('index');
        }

        $this->view->filename = $this->_store->confirmFile;
        $this->view->form = $form;
    }

    /**
     * Finish Action
     */
    public function finishAction()
    {
        $filename = APPLICATION_PATH . $this->_config;

        $config = $this->_store->config->production->resources;

        $config->frontController->defaultModule = 'users';
        $config->layout->layout = 'default/layout';

        $writer = new Core_Config_Writer_Yaml();
        $writer->setConfig($this->_store->config);
        if (is_writable($filename)) {

            $writer->write($filename);

            $this->_store->unsetAll();

            //TODO remove install module

            $this->_helper->flashMessenger('Installization complete');
            $this->_helper->redirector(false, false, false);
        } else {
            $this->view->filename = $filename;
            $this->view->config = $writer->render();
        }
    }
}

