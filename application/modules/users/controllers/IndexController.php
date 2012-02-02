<?php
/**
 * UsersController for users module
 *
 * @category   Application
 * @package    Users
 * @subpackage Controller
 *
 * @version  $Id: IndexController.php 48 2010-02-12 13:23:39Z AntonShevchuk $
 */
class Users_IndexController extends Core_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
        parent::init();
    }

    public function indexAction()
    {
        Zend_Loader_Autoloader::getInstance()->registerNamespace('ZendDbSchema');

        //$schema = new ZendDbSchema_Db_Schema_Table('forum_post');

        $schema = new ZendDbSchema_Db_Schema_Table('users1');

        var_dump($schema->isDirty(), $schema->__toString(), $schema->toArray());
        // action body
    }

    /**
     * Render profile of user
     */
    public function profileAction()
    {
        // action body
    }

}



