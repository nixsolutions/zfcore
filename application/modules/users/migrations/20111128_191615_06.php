<?php

set_include_path(realpath(dirname(__FILE__) . '/../models') . PATH_SEPARATOR . get_include_path());

require_once 'Users/Manager.php';
require_once 'User.php';
require_once 'Users/Table.php';

class Users_Migration_20111128_191615_06 extends Core_Migration_Abstract
{
    /**
     *
     * @var Users_Model_Users_Manager
     */
    private $_manager;

    public function __construct()
    {
        $this->_manager = new Users_Model_Users_Manager();
    }

    public function up()
    {
        $user = $this->_manager->register(array(
            'login' => 'admin',
            'password' => '123456',
            'email' => 'zfc.cmf@gmail.com'
        ));

        $user->role = Users_Model_User::ROLE_ADMIN;
        $user->status = Users_Model_User::STATUS_ACTIVE;

        $user->save();
    }

    public function down()
    {
        $user = $this->_manager->getDbTable()->findByLogin('admin');

        /* @var $user Users_Model_User */
        if ($user) {
            $user->delete();
        }
    }


}

