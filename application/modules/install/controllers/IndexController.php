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

    protected $_config = '/configs/application.yaml';

    /**
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        $this->_helper->layout->setLayout('install/layout');
        $this->view->stylesheet()->module();

        $this->_store = new Zend_Session_Namespace(self::SESSION_KEY);

        if (!$this->_store->config) {
            $this->_store->config = new Core_Config_Yaml(
                APPLICATION_PATH . $this->_config . '.dist',
                null,
                array(
                    'allowModifications' => true,
                    'ignoreConstants' => true,
                    'skipExtends' => true
                )
            );
        }

        if (!$this->_store->progress) {
            $this->_store->progress = array(
                'install-index-requirements' => null,
                'install-index-settings'     => false,
                'install-index-database'     => false,
                'install-index-mail'         => false,
                'install-index-api'          => false,
                'install-index-confirm'      => false,
                'install-index-migrations'   => false
            );
        }

        $this->view->pages = array(
            'install-index-requirements' => 'Requirements',
            'install-index-settings'     => 'General',
            'install-index-database'     => 'Database',
            'install-index-mail'         => 'Mail',
            'install-index-api'          => 'Integration',
            'install-index-confirm'      => 'Confirmation',
        );

        $this->view->progress = $this->_store->progress;
    }

    /**
     * Index action
     */
    public function indexAction()
    {

        foreach ($this->_store->progress as $route => $status) {
            if (!$status) {
                $this->_store->progress[$route] = null;
                $this->_helper->redirector->gotoRoute(array(), $route);
            }
        }
        $this->_helper->redirector->gotoRoute(array(), 'install-index-finish');
    }

    /**
     * Requirements action
     */
    public function requirementsAction()
    {
        $config = APPLICATION_PATH . '/modules/install/configs/checks.yaml';
        require_once 'Zend/Config/Yaml.php';
        require_once 'Core/Config/Yaml.php';
        $result = new Core_Config_Yaml($config);
        $requirements = $result->toArray();
        $this->view->requirements = $requirements;

        $resources = $this->_store->config->production->resources;
        $resources->session->save_path = APPLICATION_PATH . '/../' . $requirements['directories']['session_dir'];

        $unwritable = array();//check directories
        foreach ($requirements['directories']  as $folder) {
            $folderPath = APPLICATION_PATH . '/../' . $folder;
            if (!is_writable($folderPath)) {
                @chmod($folderPath, 0777);
            }
            if (!is_writable($folderPath)) {
                $unwritable[$folder] = $folderPath;
            }
        }
        //remove postfix
        $phpVersion = substr(PHP_VERSION,0,strpos(PHP_VERSION, '-'));
        $this->view->phpversion = $phpVersion;

        $this->view->folders = $requirements['directories'];
        if (!empty($unwritable)) {
            $this->view->unwritable = $unwritable;
        }
        $isValidPhpVersion = false;//check PHP version
        if (version_compare($phpVersion, $requirements['minPhpVersion'], '>=')) {
            $isValidPhpVersion = true;
        }
        $this->view->isValidPhpVersion = $isValidPhpVersion;

        $phpOptions = array();//check PHP options
        foreach ($requirements['phpOptions'] as $option => $val) {
            $phpOptions[$option] = $val;
            if (ini_get($option) == $val['desired']) {
                $phpOptions[$option]['check'] = true;
            } else {
                $phpOptions[$option]['check'] = false;
            }
        }
        $this->view->phpOptions = $phpOptions;

        $phpExtensions = array();
        foreach ($requirements['extensions'] as $ext => $desc) {
            $phpExtensions[$ext] = $desc;
            if (in_array($ext, get_loaded_extensions())) {
                $phpExtensions[$ext]['check'] = true;
            } else {
                $phpExtensions[$ext]['check'] = false;
            }
        }
        $this->view->phpExtensions = $phpExtensions;

        $isValid = false;
        if (empty($unwritable) && $isValidPhpVersion) {
        	$isValid = true;
        }
        $this->view->isValid = $isValid;
        if ($this->_request->isPost() && $isValid) {

            $this->_store->progress['install-index-requirements'] = true;
            foreach ($this->_store->progress as $route => $status) {
                if (!$status) {
                    $this->_store->progress[$route] = null;
                    $this->_helper->redirector->gotoRoute(array(), $route);

                }
            }
            $this->_helper->redirector->gotoRoute(array(), 'install-index-finish');
        }
    }

    /**
     * Settings action
     */
    public function settingsAction()
    {
        $form = new Install_Form_Settings_Basic();

        $config = $this->_store->config->production;

        $form->setDefault('timezone', $config->phpSettings->date->timezone);
        $form->setDefault('baseUrl', $config->resources->frontController->baseUrl);
        $form->setDefault('title', $config->resources->view->title);

        if ($this->_request->isPost()
            && $form->isValid($this->_getAllParams())) {

            $config->phpSettings->date->timezone = $form->getValue('timezone');
            $config->resources->frontController->baseUrl = $form->getValue('baseUrl');
            $config->resources->view->title = $form->getValue('title');
            //$config->uploadDir = $form->getValue('uploadDir');

            $this->_store->progress['install-index-settings'] = true;
            $this->_helper->redirector('index');
        }
        $this->view->form = $form;
    }

    /**
     * Api action
     */
    public function apiAction()
    {
        $form = new Install_Form_Settings_Api();

        $config = $this->_store->config->production->resources->registry;

        $form->setDefault('appId', $config->fbConfig->appId);
        $form->setDefault('secret', $config->fbConfig->secret);

        $form->setDefault('twitterKey', $config->twitterConfig->consumerKey);
        $form->setDefault('twitterSecret', $config->twitterConfig->consumerSecret);

        $form->setDefault('googleKey', $config->googleConfig->consumerKey);
        $form->setDefault('googleSecret', $config->googleConfig->consumerSecret);

        if ($this->_request->isPost()
            && $form->isValid($this->_getAllParams())) {

            $config->fbConfig->appId  = $form->getValue('appId');
            $config->fbConfig->secret = $form->getValue('secret');

            $config->twitterConfig->consumerKey    = $form->getValue('twitterKey');
            $config->twitterConfig->consumerSecret = $form->getValue('twitterSecret');

            $config->googleConfig->consumerKey    = $form->getValue('googleKey');
            $config->googleConfig->consumerSecret = $form->getValue('googleSecret');

            $this->_store->progress['install-index-api'] = true;
            $this->_helper->redirector('index');
        }
        $this->view->form = $form;
    }


    /**
     * Mail action
     */
    public function mailAction()
    {
        $form = new Install_Form_Settings_Mail();

        $config = $this->_store->config->production->resources->mail;

        if ($config->transport) {
            $form->setDefault('type', $config->transport->type);
            $form->setDefault('host', $config->transport->host);
            $form->setDefault('port', $config->transport->port);
        }
        if ($config->transport) {
            $form->setDefault('email', $config->defaultFrom->email);
            $form->setDefault('name', $config->defaultFrom->name);
        }

        if ($this->_request->isPost()
            && $form->isValid($this->_getAllParams())) {

            $config = array(
                'transport' => array(
                    'type' => $form->getValue('type'),
                    'host' => $form->getValue('host'),
                    'port' => $form->getValue('port')
                ),
                'defaultFrom' => array(
                    'email' => $form->getValue('email'),
                    'name' => $form->getValue('name')
                )
            );
            $config = new Zend_Config($config);
            $this->_store->config->production->resources->mail = $config;

            $this->_store->progress['install-index-mail'] = true;
            $this->_helper->redirector('index');
        }
        $this->view->form = $form;
    }

    /**
     * Database Action
     */
    public function databaseAction()
    {
        $form = new Install_Form_Settings_Database();

        if ($config = $this->_store->config->production->resources->db) {
            $form->setDefault('adapter', $config->adapter);

            $form->setDefault('host', $config->params->host);
            $form->setDefault('username', $config->params->username);
            $form->setDefault('password', $config->params->password);
            $form->setDefault('dbname', $config->params->dbname);
            $form->setDefault('charset', $config->params->charset);
        }

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

                $this->_store->progress['install-index-database'] = true;
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
        $this->_store->progress['install-index-migrations'] = true;
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

            $this->_store->progress['install-index-confirm'] = true;
            $this->_helper->redirector('index');
        }

        $this->view->filename = realpath($this->_store->confirmFile);
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

