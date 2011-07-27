<?php
/**
 * Helper_Facebook
 *
 * @version $Id$
 */
class Helper_Facebook extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @var Facebook_Facebook
     */
    protected $_client;

    const FB_APP_URL = "http://apps.facebook.com/";

    /**
     * Init fb client
     */
    public function direct()
    {
        return $this->getClient();
    }

    /**
     * Get fb client
     *
     * @return Facebook_Facebook
     */
    public function getClient()
    {
        if (!$this->_client) {
            if (!Zend_Registry::isRegistered('fbConfig')) {
                throw new Zend_Controller_Action_Exception(
                    'FbConnect Config not found'
                );
            }
            $config = Zend_Registry::get('fbConfig');

            $this->setClient($client = new Facebook_Facebook($config));
        }

        return $this->_client;
    }

    /**
     * Set client
     *
     * @param Facebook_Facebook $client
     * @return Helper_Facebook
     */
    public function setClient($client)
    {
        $this->_client = $client;

        return $this;
    }

    /**
     * Login
     *
     */
    public function login()
    {
        $user = $this->getClient()->api("/me");

        $users = new Users_Model_Users_Table();
        if (!$row = $users->getByEmail($user['email'])) {
            $row = $users->createRow();
            $row->login = $user['username'];
            $row->email = $user['email'];
            $row->firstname = $user['first_name'];
            $row->lastname = $user['last_name'];
            $row->role = Users_Model_User::ROLE_USER;
            $row->status = Users_Model_User::STATUS_ACTIVE;
        }
        $row->fbUid = $user['id'];
        $row->logined = date('Y-m-d H:i:s');
        $row->ip = $this->getRequest()->getClientIp();
        $row->count++;
        $row->save();

        $row->login();
    }
}