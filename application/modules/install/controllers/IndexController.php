<?php
/**
 * Install_IndexController for install module
 *
 * @category   Application
 * @package    Install
 * @subpackage Controller
 */
class Install_IndexController extends Core_Controller_Action
{
    const SESSION_KEY = 'Install';

    /**
     * @var Zend_Session_Namespace
     */
    protected $_store;

    protected $_configFile = 'application.yaml';
    protected $_configDir = '';

    /**
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        $this->_helper->layout->setLayout('install/layout');

        $this->_configDir = APPLICATION_PATH . '/configs/';
        $this->_store = new Zend_Session_Namespace(self::SESSION_KEY);

        if (!$this->_store->config) {
            $this->_store->config = new Core_Config_Yaml(
                $this->_configDir . $this->_configFile . '.dist',
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
                'install-index-modules'      => false,
                'install-index-admin'        => false,
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
            'install-index-modules'      => 'Modules',
            'install-index-admin'        => 'Admin',
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
        
        $isValid = true;

        $rootDirectory = realpath(APPLICATION_PATH . '/../');
        $result = new Core_Config_Yaml($config);
        
        $requirements = $result->toArray();
        $this->view->requirements = $requirements;

        $resources = $this->_store->config->production->resources;
        $resources->session->save_path = APPLICATION_PATH . '/../' . $requirements['directories']['session_dir'];

        $directories = array();//check directories

        foreach ($requirements['directories'] as $directory) {
            
            $info = array();
            
            $info['path'] = $rootDirectory . '/' . $directory;         
            $info['exists'] = file_exists($info['path']);
            $info['writable'] = $info['exists'] && is_writable($info['path']);
            
            if (!$info['writable']) {
                $isValid = false;
            }
            
            $directories[$directory] = $info;
        }
        
        
        $this->view->directories = $directories;
        
        //remove postfix
        $phpVersion = PHP_VERSION;
        $this->view->phpversion = $phpVersion;
        
        $isValidPhpVersion = false;//check PHP version
        if (version_compare($phpVersion, $requirements['minPhpVersion'], '>=')) {
            $isValidPhpVersion = true;
        }
        $this->view->isValidPhpVersion = $isValidPhpVersion;
        
        if (!$isValidPhpVersion) {
            $isValid = false;
        }
        
        $this->view->isValid = $isValid;

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

        //check PHP extensions
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
        $this->view->currentPage = 'install-index-requirements';
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
        $this->view->currentPage = 'install-index-settings';
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
        $this->view->currentPage = 'install-index-api';
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
        $this->view->currentPage = 'install-index-mail';
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
        $this->view->currentPage = 'install-index-database';
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
            'modulesDirectoryPath' => APPLICATION_PATH . '/modules',
        );

        $manager = new Core_Migration_Manager($options);

        $manager->up();

        $pathToModules = APPLICATION_PATH . '/modules/';
        foreach ($this->_store->modules as $module => $value) {//up installed modules migrations
            if ($value === false) {
                $pathToModule = $pathToModules . $module;
                if (is_dir($pathToModules) && is_writable($pathToModules) && is_dir($pathToModule)) {
                    rename($pathToModule, APPLICATION_PATH . '/modules/.' . $module);
                }
            } else {
                $manager->up($module);
                if ($module === 'menu') {
                    $this->_store->config->production->resources->navigation->source->default = 'db';
                }
            }
        }
        unset($this->_store->modules);

        $usersTable = new Users_Model_User_Table();
        //update or create admin
        if (!$usersTable->update($this->_store->user, 'login = "' . $this->_store->user['login'] . '"')) {
            $usersTable->insert($this->_store->user);
        }
        unset($this->_store->user);

        $this->_helper->flashMessenger('Migrations rolled up');
        $this->_store->progress['install-index-migrations'] = true;
        $this->_helper->redirector('index');
        $this->view->currentPage = 'install-index-migrations';
    }

    /**
     * Admin action
     */
    public function adminAction()
    {
        $form = new Install_Form_Settings_Admin();
        if ($this->_request->isPost()
            && $form->isValid($this->_getAllParams())) {

            $user = array();
            $user['login']    = $this->_getParam('login');
            $user['email']    = $this->_getParam('email');
            $user['role']     = Users_Model_User::ROLE_ADMIN;
            $user['status']   = Users_Model_User::STATUS_ACTIVE;
            $user['hashCode'] = md5($this->_getParam('login') . uniqid());
            $user['salt']     = md5(uniqid());
            $user['password'] = md5($user['salt'] . $this->_getParam('password'));

            $this->_store->user = $user;

            $this->_store->progress['install-index-admin'] = true;
            $this->_helper->redirector('index');
        }
        $this->view->form = $form;
        $this->view->currentPage = 'install-index-admin';
    }

    /**
     * Admin action
     */
    public function modulesAction()
    {
        $config = APPLICATION_PATH . '/modules/install/configs/modules.yaml';
        require_once 'Zend/Config/Yaml.php';
        require_once 'Core/Config/Yaml.php';
        $result = new Core_Config_Yaml($config);
        $modules = $result->modules->toArray();

        $isWritableModulesDir = true;
        $installModules = array();
        $pathToModules = APPLICATION_PATH . '/modules/';
        if (!is_writable($pathToModules)) {
            @chmod($pathToModules, 0777);
            if (!is_writable($pathToModules)) {
                $isWritableModulesDir = false;
            }
        }

        foreach ($modules as $module => $value) {
            if (!$isWritableModulesDir) {
                $modules[$module]['required'] = true;
                $installModules[$module] = true;
            } else {
                $installModules[$module] = $modules[$module]['required'];
            }
            if ($this->_store->modules) {
                $modules[$module]['checked'] = $this->_store->modules[$module];
            } else {
                $modules[$module]['checked'] = true;
            }

        }
        $this->view->modules = $modules;
        $this->view->isDisabled = !$isWritableModulesDir;

        if ($this->_request->isPost() && !$this->_request->getParam('refresh')) {

            if ($this->_request->getParam('modules')) {
                $installModules = array_merge($installModules, $this->_request->getParam('modules'));
            } elseif ($this->_request->getParam('isDisabled')) {
                // In case if the selection of modules has been locked
                foreach ($modules as $module => $value) {
                    $installModules[$module] = true;
                }
            }

            $this->_store->modules = $installModules;
            $this->_store->progress['install-index-modules'] = true;
            $this->_helper->redirector('index');
        }
        $this->view->currentPage = 'install-index-modules';
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
        $this->view->currentPage = 'install-index-confirm';
    }

    /**
     * Finish Action
     */
    public function finishAction()
    {
        $filename = $this->_configDir . $this->_configFile;

        $config = $this->_store->config->production->resources;

        $config->frontController->defaultModule = 'index';
        $config->layout->layout = 'default/layout';

        $writer = new Core_Config_Writer_Yaml();
        $writer->setConfig($this->_store->config);
        if (is_writable($this->_configDir)) {

            $writer->write($filename);
            $this->_store->unsetAll();

            //remove install module

            $pathToModules = APPLICATION_PATH . '/modules/';
            $module = 'install';
            $pathToModule = $pathToModules . $module;
            if (is_dir($pathToModules) && is_writable($pathToModules) && is_dir($pathToModule)) {
                rename($pathToModule, APPLICATION_PATH . '/modules/.' . $module);
                $this->_helper->flashMessenger($this->view->__('Remove module Install'));
            }

            $this->_helper->flashMessenger("Installation complete");
            $this->_helper->redirector(false, false, false);
        } else {
            $this->view->filename = $filename;
            $this->view->config = $writer->render();
        }
    }
}

