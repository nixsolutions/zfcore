<?php
/**
 * IndexController for default module
 *
 * @category   Application
 * @package    Default
 * @subpackage Controller
 * 
 * @version  $Id: IndexController.php 146 2010-07-05 14:22:20Z AntonShevchuk $
 */
class IndexController extends Core_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
        parent::init();

    }

    public function indexAction()
    {
//        $config = new Core_Config_Json(APPLICATION_PATH . '/configs/application.json', 'testing');
//        var_dump($config->toArray());
//        $config = new Core_Config_Yaml(APPLICATION_PATH . '/configs/application.yaml', 'testing');
//        var_dump($config->toArray());
    }
}